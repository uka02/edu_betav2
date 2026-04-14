<?php

namespace App\Http\Controllers;

use App\Models\LessonFeedback;
use App\Models\LessonReport;
use App\Models\Lesson;
use App\Services\LessonExamService;
use App\Services\LessonProgressService;
use App\Support\Lessons\LessonPublishChecklist;
use App\Traits\HandleLessonSegments;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use InvalidArgumentException;
use Throwable;

class LessonController extends Controller
{
    use HandleLessonSegments;

    public function index(Request $request)
    {
        $user = $request->user();

        if ($this->isLessonManager($user)) {
            $isAdminLessonWorkspace = $user->isAdmin();
            $lessons = Lesson::query()
                ->with('user:id,name')
                ->when(! $isAdminLessonWorkspace, fn ($query) => $query->where('user_id', Auth::id()))
                ->latest()
                ->paginate(12);

            return view('lessons.index', [
                'lessons' => $lessons,
                'isAdminLessonWorkspace' => $isAdminLessonWorkspace,
                'canCreateLessons' => $user->isEducator(),
                'showLessonOwner' => $isAdminLessonWorkspace,
            ]);
        }

        $filters = [
            'q' => trim((string) $request->query('q', '')),
            'subject' => $this->resolveCatalogSubject($request->query('subject')),
            'difficulty' => $this->resolveCatalogDifficulty($request->query('difficulty')),
            'price' => $this->resolveCatalogPrice($request->query('price')),
            'sort' => $this->resolveCatalogSort($request->query('sort')),
        ];

        $catalogQuery = Lesson::query()
            ->published()
            ->with('user')
            ->withCount([
                'progressRecords as learner_count',
                'certificates as certificate_count',
            ])
            ->when($filters['q'] !== '', fn ($query) => $this->applyCatalogSearch($query, $filters['q']))
            ->when($filters['subject'] !== null, fn ($query) => $query->bySubject($filters['subject']))
            ->when($filters['difficulty'] !== null, fn ($query) => $query->where('difficulty', $filters['difficulty']))
            ->when($filters['price'] === 'free', fn ($query) => $query->where('is_free', true))
            ->when($filters['price'] === 'paid', fn ($query) => $query->where('is_free', false));

        $catalogQuery = match ($filters['sort']) {
            'popular' => $catalogQuery->orderByDesc('learner_count')->orderByDesc('certificate_count')->latest(),
            'quickest' => $catalogQuery->orderBy('duration_minutes')->latest(),
            'longest' => $catalogQuery->orderByDesc('duration_minutes')->latest(),
            default => $catalogQuery->latest(),
        };

        $lessons = $catalogQuery->paginate(12)->withQueryString();
        $this->attachLearnerLessonState($user, $lessons->getCollection());

        $subjectCounts = Lesson::query()
            ->published()
            ->pluck('subject')
            ->countBy(fn ($subject) => Lesson::normalizeSubject($subject));

        return view('lessons.learner-index', [
            'lessons' => $lessons,
            'filters' => $filters,
            'subjectOptions' => Lesson::subjectOptions(),
            'subjectCounts' => $subjectCounts,
            'startedLessonsCount' => $user
                ? $user->lessonProgress()
                    ->whereHas('lesson', fn ($query) => $query->published())
                    ->count()
                : 0,
            'completedLessonsCount' => $user
                ? $user->lessonProgress()
                    ->whereHas('lesson', fn ($query) => $query->published())
                    ->where('progress_percent', '>=', 100)
                    ->count()
                : 0,
            'publishedCatalogCount' => Lesson::published()->count(),
            'learnerCertificateCount' => $user ? $user->certificates()->count() : 0,
        ]);
    }

    public function create()
    {
        return view('lessons.create');
    }

    public function store(Request $request, LessonPublishChecklist $publishChecklist)
    {
        $saveAsDraft = $this->isDraftSave($request);
        $validated = $this->validateLessonRequest($request, $publishChecklist, $saveAsDraft);
        $payload = $this->prepareLessonPayload($request, $validated, null, $saveAsDraft);

        $lesson = Lesson::create($payload);

        if ($saveAsDraft) {
            return redirect()
                ->route('lessons.edit', $lesson)
                ->with('clear_lesson_draft_keys', ['lesson-builder-draft:create'])
                ->with('success', __('lessons.draft_saved'));
        }

        return redirect()->route('lessons.index')->with('success', __('lessons.lesson_created'));
    }

    public function autosave(Request $request, LessonPublishChecklist $publishChecklist)
    {
        $lesson = null;
        $lessonId = $request->input('lesson_id');

        if (filled($lessonId)) {
            $lesson = Lesson::query()
                ->whereKey($lessonId)
                ->firstOrFail();
            $this->ensureLessonManageAccess($lesson);

            if ($lesson->is_published) {
                return response()->json([
                    'message' => __('lessons.draft_autosave_unavailable'),
                ], 422);
            }
        }

        $validated = $this->validateLessonRequest($request, $publishChecklist, true, $lesson !== null, $lesson);
        $payload = $this->prepareLessonPayload($request, $validated, $lesson, true);
        $wasRecentlyCreated = $lesson === null;

        if ($lesson) {
            $lesson->update($payload);
        } else {
            $lesson = Lesson::create($payload);
        }

        $lesson->refresh();

        return response()->json([
            'lesson_id' => $lesson->id,
            'saved_at' => $lesson->updated_at?->toIso8601String(),
            'created' => $wasRecentlyCreated,
            'edit_url' => route('lessons.edit', $lesson),
            'update_url' => route('lessons.update', $lesson),
        ]);
    }

    public function show(Lesson $lesson, LessonProgressService $lessonProgressService)
    {
        $this->ensureLessonIsViewable($lesson);

        $viewer = Auth::user();
        $lessonProgress = $viewer
            ? $lessonProgressService->syncStoredProgress(
                $viewer->lessonProgress()
                    ->where('lesson_id', $lesson->id)
                    ->first(),
                $lesson,
            )
            : null;
        $canManageLesson = $this->canManageLesson($viewer, $lesson);
        $lessonEngagementEnabled = $this->lessonEngagementTablesAvailable();
        $structuredLessonFeedbackEnabled = $this->lessonFeedbackSupportsStructuredFields();
        $currentUserFeedback = null;
        $currentUserReport = null;
        $recentLessonFeedback = collect();
        $recentLessonReports = collect();
        $feedbackAverageRating = null;
        $feedbackCount = 0;
        $reportCount = 0;

        if ($lessonEngagementEnabled) {
            $feedbackSummary = $lesson->feedbackEntries()
                ->selectRaw('COUNT(*) as feedback_count')
                ->selectRaw('COALESCE(AVG(rating), 0) as average_rating')
                ->first();
            $currentUserFeedback = $viewer && ! $canManageLesson
                ? $lesson->feedbackEntries()->where('user_id', $viewer->id)->first()
                : null;
            $currentUserReport = $viewer && ! $canManageLesson
                ? $lesson->reports()->where('user_id', $viewer->id)->first()
                : null;
            $recentLessonFeedback = $canManageLesson
                ? $lesson->feedbackEntries()->with('user:id,name')->latest()->take(8)->get()
                : collect();
            $recentLessonReports = $canManageLesson
                ? $lesson->reports()->with('user:id,name')->latest()->take(8)->get()
                : collect();
            $feedbackAverageRating = (int) ($feedbackSummary->feedback_count ?? 0) > 0
                ? round((float) ($feedbackSummary->average_rating ?? 0), 1)
                : null;
            $feedbackCount = (int) ($feedbackSummary->feedback_count ?? 0);
            $reportCount = $canManageLesson ? $lesson->reports()->count() : 0;
        }

        return view('lessons.show', [
            'lesson' => $lesson,
            'lessonProgress' => $lessonProgress,
            'canManageLesson' => $canManageLesson,
            'lessonEngagementEnabled' => $lessonEngagementEnabled,
            'structuredLessonFeedbackEnabled' => $structuredLessonFeedbackEnabled,
            'currentUserFeedback' => $currentUserFeedback,
            'currentUserReport' => $currentUserReport,
            'recentLessonFeedback' => $recentLessonFeedback,
            'recentLessonReports' => $recentLessonReports,
            'feedbackAverageRating' => $feedbackAverageRating,
            'feedbackCount' => $feedbackCount,
            'reportCount' => $reportCount,
        ]);
    }

    public function storeFeedback(Request $request, Lesson $lesson)
    {
        $user = $request->user();
        $this->ensureLessonInteractionIsAllowed($lesson, $user);

        if (! $this->lessonEngagementTablesAvailable()) {
            return redirect()
                ->route('lessons.show', $lesson)
                ->with('error', __('lessons.lesson_engagement_unavailable'));
        }

        $structuredLessonFeedbackEnabled = $this->lessonFeedbackSupportsStructuredFields();
        $validated = $request->validateWithBag('feedback', [
            'rating' => ['required', 'integer', 'min:1', 'max:5'],
            'feedback' => ['nullable', 'string', 'max:3000'],
            'positive_feedback' => ['nullable', 'string', 'max:3000'],
            'negative_feedback' => ['nullable', 'string', 'max:3000'],
        ]);

        $legacyFeedback = $this->normalizeOptionalText($validated['feedback'] ?? null);
        $positiveFeedback = $structuredLessonFeedbackEnabled
            ? $this->normalizeOptionalText($validated['positive_feedback'] ?? null)
            : null;
        $negativeFeedback = $structuredLessonFeedbackEnabled
            ? $this->normalizeOptionalText($validated['negative_feedback'] ?? null)
            : null;

        if ($structuredLessonFeedbackEnabled && $positiveFeedback === null && $negativeFeedback === null && $legacyFeedback !== null) {
            $positiveFeedback = $legacyFeedback;
        }

        $feedbackPayload = [
            'rating' => $validated['rating'],
            'feedback' => $structuredLessonFeedbackEnabled
                ? $this->buildLegacyFeedbackSummary($positiveFeedback, $negativeFeedback, $legacyFeedback)
                : $legacyFeedback,
        ];

        if ($structuredLessonFeedbackEnabled) {
            $feedbackPayload['positive_feedback'] = $positiveFeedback;
            $feedbackPayload['negative_feedback'] = $negativeFeedback;
        }

        LessonFeedback::updateOrCreate(
            [
                'lesson_id' => $lesson->id,
                'user_id' => $user->id,
            ],
            $feedbackPayload
        );

        return redirect()
            ->route('lessons.show', $lesson)
            ->with('success', __('lessons.feedback_saved'));
    }

    public function storeReport(Request $request, Lesson $lesson)
    {
        $user = $request->user();
        $this->ensureLessonInteractionIsAllowed($lesson, $user);

        if (! $this->lessonEngagementTablesAvailable()) {
            return redirect()
                ->route('lessons.show', $lesson)
                ->with('error', __('lessons.lesson_engagement_unavailable'));
        }

        $validated = $request->validateWithBag('report', [
            'reason' => ['required', Rule::in(LessonReport::reasonOptions())],
            'details' => ['required', 'string', 'max:3000'],
        ]);

        LessonReport::updateOrCreate(
            [
                'lesson_id' => $lesson->id,
                'user_id' => $user->id,
            ],
            [
                'reason' => $validated['reason'],
                'details' => trim((string) $validated['details']),
            ]
        );

        return redirect()
            ->route('lessons.show', $lesson)
            ->with('success', __('lessons.report_submitted'));
    }

    public function previewMainDocument(Lesson $lesson)
    {
        $document = $this->resolveMainDocument($lesson);

        return $this->renderDocumentPreview(
            $lesson,
            $document['path'],
            route('lessons.documents.stream', $lesson),
            route('lessons.documents.stream', ['lesson' => $lesson, 'download' => 1]),
            __('lessons.preview_document'),
            __('lessons.download_document')
        );
    }

    public function streamMainDocument(Request $request, Lesson $lesson)
    {
        $document = $this->resolveMainDocument($lesson);

        return $this->streamDocumentFile(
            $document['path'],
            $document['name'],
            $request->boolean('download')
        );
    }

    public function previewBlockFile(Lesson $lesson, int $segment, int $block)
    {
        $fileBlock = $this->resolveBlockFile($lesson, $segment, $block);

        return $this->renderDocumentPreview(
            $lesson,
            $fileBlock['path'],
            route('lessons.block-files.stream', [
                'lesson' => $lesson,
                'segment' => $segment,
                'block' => $block,
            ]),
            route('lessons.block-files.stream', [
                'lesson' => $lesson,
                'segment' => $segment,
                'block' => $block,
                'download' => 1,
            ]),
            __('lessons.preview_file'),
            __('lessons.download_file')
        );
    }

    public function streamBlockFile(Request $request, Lesson $lesson, int $segment, int $block)
    {
        $fileBlock = $this->resolveBlockFile($lesson, $segment, $block);

        return $this->streamDocumentFile(
            $fileBlock['path'],
            $fileBlock['name'],
            $request->boolean('download')
        );
    }

    public function edit(Lesson $lesson)
    {
        $this->ensureLessonManageAccess($lesson);

        return view('lessons.edit', compact('lesson'));
    }

    public function update(Request $request, Lesson $lesson, LessonPublishChecklist $publishChecklist)
    {
        $this->ensureLessonManageAccess($lesson);

        $saveAsDraft = $this->isDraftSave($request);
        $validated = $this->validateLessonRequest($request, $publishChecklist, $saveAsDraft, true, $lesson);
        $payload = $this->prepareLessonPayload($request, $validated, $lesson, $saveAsDraft);

        $lesson->update($payload);

        if ($saveAsDraft) {
            return redirect()
                ->route('lessons.edit', $lesson)
                ->with('clear_lesson_draft_keys', ["lesson-builder-draft:edit:{$lesson->id}"])
                ->with('success', __('lessons.draft_saved'));
        }

        return redirect()->route('lessons.show', $lesson)->with('success', __('lessons.lesson_updated'));
    }

    public function publish(Lesson $lesson, LessonPublishChecklist $publishChecklist)
    {
        $this->ensureLessonManageAccess($lesson);

        if (! $publishChecklist->evaluateLesson($lesson)['ready']) {
            return redirect()
                ->route('lessons.edit', $lesson)
                ->withErrors(['publish_checklist' => __('lessons.complete_before_publish')])
                ->with('error', __('lessons.complete_before_publish'));
        }

        if (! $lesson->is_published) {
            $lesson->forceFill([
                'is_published' => true,
            ])->save();
        }

        return back()->with('success', __('lessons.lesson_published'));
    }

    public function destroy(Lesson $lesson)
    {
        $this->ensureLessonManageAccess($lesson);

        $lesson->delete();

        return redirect()->route('lessons.index')->with('success', __('lessons.lesson_deleted'));
    }

    public function forceDelete($id)
    {
        $lesson = Lesson::withTrashed()->findOrFail($id);
        $this->ensureLessonManageAccess($lesson);

        if ($lesson->thumbnail) {
            Storage::disk('public')->delete($lesson->thumbnail);
        }

        if ($lesson->document_path) {
            Storage::disk('public')->delete($lesson->document_path);
        }

        foreach ($lesson->segments ?? [] as $segment) {
            if (! isset($segment['blocks']) || ! is_array($segment['blocks'])) {
                continue;
            }

            foreach ($segment['blocks'] as $block) {
                if (
                    isset($block['path']) &&
                    in_array($block['type'], ['image', 'file'], true)
                ) {
                    Storage::disk('public')->delete($block['path']);
                }
            }
        }

        $lesson->forceDelete();

        return redirect()->route('lessons.index')->with('success', __('lessons.lesson_permanently_deleted'));
    }

    public function restore($id)
    {
        $lesson = Lesson::withTrashed()->findOrFail($id);
        $this->ensureLessonManageAccess($lesson);

        $lesson->restore();

        return redirect()->route('lessons.show', $lesson)->with('success', __('lessons.lesson_restored'));
    }

    public function checkTitle(Request $request)
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'exclude_id' => ['nullable', 'integer'],
        ]);

        $titleOwnerId = $this->resolveLessonTitleOwnerId($request->user(), $validated['exclude_id'] ?? null);
        $query = Lesson::where('user_id', $titleOwnerId)
            ->where('title', $validated['title']);

        if (! empty($validated['exclude_id'])) {
            $query->where('id', '!=', $validated['exclude_id']);
        }

        return response()->json(['exists' => $query->exists()]);
    }

    public function gradeExam(Request $request, Lesson $lesson, LessonExamService $lessonExamService)
    {
        if ($lesson->user_id !== Auth::id() && ! $lesson->is_published) {
            abort(403, __('lessons.unauthorized'));
        }

        $validated = $request->validate([
            'exam_index' => 'required|integer|min:0',
            'answers' => 'required|array',
            'time_taken' => 'required|integer|min:0',
        ]);

        try {
            $result = $lessonExamService->gradeAndRecord(
                $lesson,
                $request->user(),
                $validated['exam_index'],
                $validated['answers'],
                $validated['time_taken'],
            );
        } catch (InvalidArgumentException) {
            return response()->json(['success' => false, 'message' => 'Exam not found'], 404);
        } catch (Throwable $e) {
            Log::error('Lesson exam submission failed.', [
                'lesson_id' => $lesson->id,
                'user_id' => $request->user()?->id,
                'exam_index' => $validated['exam_index'],
                'message' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => __('lessons.exam_submit_failed'),
            ], 500);
        }

        $certificate = $result['certificate'];

        return response()->json([
            'success' => true,
            'score' => $result['score'],
            'passed' => $result['passed'],
            'correct_count' => $result['correct_count'],
            'total_questions' => $result['total_questions'],
            'time_taken' => $result['time_taken'],
            'results' => $result['results'],
            'time_limit_exceeded' => $result['time_limit_exceeded'],
            'message' => $result['time_limit_exceeded']
                ? __('lessons.exam_time_limit_exceeded')
                : null,
            'attempt_id' => $result['attempt']->id,
            'certificate' => $certificate ? [
                'id' => $certificate->id,
                'code' => $certificate->certificate_code,
                'issued_at' => $certificate->issued_at?->toIso8601String(),
                'was_issued_now' => $certificate->wasRecentlyCreated,
            ] : null,
        ]);
    }

    private function isDraftSave(Request $request): bool
    {
        return $request->input('save_action') === 'draft';
    }

    private function validateLessonRequest(
        Request $request,
        LessonPublishChecklist $publishChecklist,
        bool $saveAsDraft,
        bool $isUpdate = false,
        ?Lesson $lesson = null
    ): array {
        $validator = Validator::make(
            $request->all(),
            $this->lessonValidationRules($saveAsDraft, $isUpdate)
        );

        $validator->after(function ($validator) use ($request, $publishChecklist, $saveAsDraft, $lesson) {
            if ($saveAsDraft || ! $request->boolean('is_published')) {
                return;
            }

            $publishState = $publishChecklist->evaluateRequest($request, $lesson);

            if ($publishState['ready']) {
                return;
            }

            $validator->errors()->add('publish_checklist', __('lessons.complete_before_publish'));
        });

        return $validator->validate();
    }

    private function lessonValidationRules(bool $saveAsDraft, bool $isUpdate = false): array
    {
        return [
            'title' => ($saveAsDraft ? 'nullable' : 'required') . '|string|max:255',
            'subject' => 'nullable|in:' . implode(',', Lesson::validSubjectInputs()),
            'type' => ($saveAsDraft ? 'nullable' : 'required') . '|in:video,text,document',
            'video_url' => $saveAsDraft
                ? 'nullable|url'
                : 'required_if:type,video|nullable|url',
            'document' => $saveAsDraft
                ? 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480'
                : ($isUpdate
                    ? 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480'
                    : 'required_if:type,document|nullable|file|mimes:pdf,doc,docx,xls,xlsx,ppt,pptx|max:20480'),
            'thumbnail' => 'nullable|image|mimes:png,jpg,jpeg,gif|max:5120',
            'duration_hours' => 'nullable|integer|min:0|max:99',
            'duration_minutes' => 'nullable|integer|min:0|max:59',
            'difficulty' => 'nullable|in:beginner,intermediate,advanced',
            'is_published' => 'nullable|boolean',
            'is_free' => 'nullable|boolean',
            'segments' => 'nullable|array',
            'segments.*.custom_name' => 'nullable|string|max:255',
            'segments.*.blocks' => 'nullable|array',
            'segments.*.blocks.*.type' => 'required_with:segments.*.blocks|in:text,subheading,image,video,file,quiz,callout,code,divider',
            'segments.*.blocks.*.content' => 'nullable|string',
            'segments.*.blocks.*.image' => 'nullable|image|mimes:png,jpg,jpeg,gif|max:5120',
            'segments.*.blocks.*.file' => 'nullable|file|max:20480',
            'segments.*.blocks.*.callout_type' => 'nullable|in:info,warning,success,danger',
            'segments.*.blocks.*.language' => 'nullable|string|max:50',
            'segments.*.blocks.*.question' => 'nullable|string|max:1000',
            'segments.*.blocks.*.answers' => 'nullable|array',
            'segments.*.blocks.*.answers.*' => 'nullable|string|max:500',
            'segments.*.blocks.*.correct_answer' => 'nullable',
            'segments.*.exam_settings.time_limit' => 'nullable|integer|min:0',
            'segments.*.exam_settings.passing_score' => 'nullable|integer|min:0|max:100',
            'segments.*.questions' => 'nullable|array',
            'segments.*.questions.*.type' => 'required_with:segments.*.questions|in:multiple_choice,true_false,short_answer',
            'segments.*.questions.*.question' => 'required_with:segments.*.questions|string|max:1000',
            'segments.*.questions.*.correct_answer' => 'required_with:segments.*.questions',
            'segments.*.questions.*.answers' => 'nullable|array',
            'segments.*.questions.*.answers.*' => 'nullable|string|max:500',
            'segments.*.questions.*.case_sensitive' => 'nullable|boolean',
        ];
    }

    private function prepareLessonPayload(
        Request $request,
        array $validated,
        ?Lesson $lesson = null,
        bool $saveAsDraft = false
    ): array {
        $validated['user_id'] = $lesson?->user_id ?? Auth::id();
        $validated['title'] = $this->resolveLessonTitle($validated, $lesson, $saveAsDraft);
        $validated['subject'] = Lesson::normalizeSubject($validated['subject'] ?? $lesson?->subject ?? Lesson::defaultSubject());
        $validated['type'] = $validated['type'] ?? $lesson?->type ?? 'text';
        $validated['is_published'] = $saveAsDraft ? false : $request->has('is_published');
        $validated['is_free'] = $request->has('is_free');
        $validated['duration_minutes'] = ((int) $request->input('duration_hours', 0) * 60)
            + (int) $request->input('duration_minutes', 0);

        if ($validated['type'] !== 'video') {
            $validated['video_url'] = null;
        }

        if ($request->hasFile('thumbnail')) {
            if ($lesson?->thumbnail) {
                Storage::disk('public')->delete($lesson->thumbnail);
            }

            $validated['thumbnail'] = $request->file('thumbnail')->store('thumbnails', 'public');
        }

        if ($request->hasFile('document')) {
            if ($lesson?->document_path) {
                Storage::disk('public')->delete($lesson->document_path);
            }

            $validated['document_path'] = $request->file('document')->store('documents', 'public');
        } elseif ($lesson && $validated['type'] !== 'document') {
            if ($lesson->document_path) {
                Storage::disk('public')->delete($lesson->document_path);
            }

            $validated['document_path'] = null;
        }

        if ($lesson) {
            $this->cleanupUnusedFiles($lesson->segments ?? [], $request->input('segments', []));
        }

        $validated['segments'] = $this->processSegments($request);

        return $validated;
    }

    private function resolveLessonTitle(array $validated, ?Lesson $lesson, bool $saveAsDraft): string
    {
        $title = trim((string) ($validated['title'] ?? ''));

        if ($title !== '') {
            return $title;
        }

        if ($saveAsDraft) {
            return $lesson?->title ?: __('lessons.untitled_draft');
        }

        return $title;
    }

    private function resolveCatalogSubject(mixed $subject): ?string
    {
        $value = trim((string) $subject);

        if ($value === '' || ! in_array($value, Lesson::validSubjectInputs(), true)) {
            return null;
        }

        return Lesson::normalizeSubject($value);
    }

    private function resolveCatalogDifficulty(mixed $difficulty): ?string
    {
        return in_array($difficulty, ['beginner', 'intermediate', 'advanced'], true) ? $difficulty : null;
    }

    private function resolveCatalogPrice(mixed $price): ?string
    {
        return in_array($price, ['free', 'paid'], true) ? $price : null;
    }

    private function resolveCatalogSort(mixed $sort): string
    {
        return in_array($sort, ['newest', 'popular', 'quickest', 'longest'], true) ? $sort : 'newest';
    }

    private function applyCatalogSearch($query, string $search)
    {
        $query->where(function ($lessonQuery) use ($search) {
            $lessonQuery
                ->where('title', 'like', '%' . $search . '%')
                ->orWhere('subject', 'like', '%' . $search . '%')
                ->orWhere('type', 'like', '%' . $search . '%')
                ->orWhere('difficulty', 'like', '%' . $search . '%')
                ->orWhere('content', 'like', '%' . $search . '%')
                ->orWhereHas('user', function ($userQuery) use ($search) {
                    $userQuery->where('name', 'like', '%' . $search . '%');
                });
        });

        return $query;
    }

    private function attachLearnerLessonState($user, Collection $lessons): void
    {
        $progressMap = collect();
        $certificateMap = collect();

        if ($user) {
            $progressMap = $user->lessonProgress()
                ->whereIn('lesson_id', $lessons->pluck('id'))
                ->get()
                ->keyBy('lesson_id');

            $certificateMap = $user->certificates()
                ->whereIn('lesson_id', $lessons->pluck('id'))
                ->get()
                ->keyBy('lesson_id');
        }

        $lessons->transform(function (Lesson $lesson) use ($progressMap, $certificateMap) {
            $segments = collect($lesson->segments ?? []);
            $contentCount = $segments->filter(fn ($segment) => ($segment['type'] ?? null) === 'content')->count();
            $examCount = $segments->filter(fn ($segment) => ($segment['type'] ?? null) === 'exam')->count();
            $progress = $progressMap->get($lesson->id);

            $lesson->setAttribute('content_section_count', max(1, $contentCount));
            $lesson->setAttribute('exam_segment_count', $examCount);
            $lesson->setAttribute('progress_percent', (int) ($progress?->progress_percent ?? 0));
            $lesson->setAttribute('has_started', (int) ($progress?->progress_percent ?? 0) > 0);
            $lesson->setAttribute('last_opened_at', $progress?->last_viewed_at);
            $lesson->setAttribute('has_certificate', $certificateMap->has($lesson->id));

            return $lesson;
        });
    }

    private function ensureLessonIsViewable(Lesson $lesson): void
    {
        $viewer = Auth::user();

        if (! $this->canManageLesson($viewer, $lesson) && ! $lesson->is_published) {
            abort(403, __('lessons.unauthorized'));
        }
    }

    private function ensureLessonInteractionIsAllowed(Lesson $lesson, $user): void
    {
        if (! $user) {
            abort(403, __('lessons.unauthorized'));
        }

        if (! $lesson->is_published || $this->canManageLesson($user, $lesson)) {
            abort(403, __('lessons.unauthorized'));
        }
    }

    private function ensureLessonManageAccess(Lesson $lesson): void
    {
        if (! $this->canManageLesson(Auth::user(), $lesson)) {
            abort(403, __('lessons.unauthorized'));
        }
    }

    private function isLessonManager($user): bool
    {
        return $user?->isEducator() || $user?->isAdmin();
    }

    private function canManageLesson($user, Lesson $lesson): bool
    {
        return (bool) $user && ($user->isAdmin() || (int) $lesson->user_id === (int) $user->id);
    }

    private function resolveLessonTitleOwnerId($user, ?int $excludeId = null): int
    {
        if ($user?->isAdmin() && $excludeId) {
            $ownerId = Lesson::withTrashed()->whereKey($excludeId)->value('user_id');

            if ($ownerId !== null) {
                return (int) $ownerId;
            }
        }

        return (int) $user?->id;
    }

    private function lessonEngagementTablesAvailable(): bool
    {
        return Schema::hasTable((new LessonFeedback())->getTable())
            && Schema::hasTable((new LessonReport())->getTable());
    }

    private function lessonFeedbackSupportsStructuredFields(): bool
    {
        $feedbackTable = (new LessonFeedback())->getTable();

        return Schema::hasTable($feedbackTable)
            && Schema::hasColumn($feedbackTable, 'positive_feedback')
            && Schema::hasColumn($feedbackTable, 'negative_feedback');
    }

    private function normalizeOptionalText(mixed $value): ?string
    {
        $text = trim((string) $value);

        return $text !== '' ? $text : null;
    }

    private function buildLegacyFeedbackSummary(?string $positiveFeedback, ?string $negativeFeedback, ?string $fallbackFeedback = null): ?string
    {
        if ($positiveFeedback === null && $negativeFeedback === null) {
            return $fallbackFeedback;
        }

        $parts = [];

        if ($positiveFeedback !== null) {
            $parts[] = __('lessons.positive_feedback') . ': ' . $positiveFeedback;
        }

        if ($negativeFeedback !== null) {
            $parts[] = __('lessons.negative_feedback') . ': ' . $negativeFeedback;
        }

        return implode("\n\n", $parts);
    }

    /**
     * @return array{path: string, name: string}
     */
    private function resolveMainDocument(Lesson $lesson): array
    {
        $this->ensureLessonIsViewable($lesson);

        if ($lesson->type !== 'document' || ! filled($lesson->document_path)) {
            abort(404);
        }

        return $this->resolveStoredDocument($lesson->document_path);
    }

    /**
     * @return array{path: string, name: string}
     */
    private function resolveBlockFile(Lesson $lesson, int $segmentId, int $blockId): array
    {
        $this->ensureLessonIsViewable($lesson);

        foreach ($lesson->segments ?? [] as $segment) {
            if ((int) ($segment['id'] ?? 0) !== $segmentId) {
                continue;
            }

            foreach (($segment['blocks'] ?? []) as $block) {
                if ((int) ($block['id'] ?? 0) !== $blockId) {
                    continue;
                }

                if (($block['type'] ?? null) !== 'file' || ! filled($block['path'] ?? null)) {
                    abort(404);
                }

                return $this->resolveStoredDocument($block['path']);
            }

            break;
        }

        abort(404);
    }

    /**
     * @return array{path: string, name: string}
     */
    private function resolveStoredDocument(string $relativePath): array
    {
        if (! Storage::disk('public')->exists($relativePath)) {
            abort(404);
        }

        return [
            'path' => $relativePath,
            'name' => basename($relativePath),
        ];
    }

    private function renderDocumentPreview(
        Lesson $lesson,
        string $relativePath,
        string $streamUrl,
        string $downloadUrl,
        string $title,
        string $downloadLabel
    ) {
        $extension = strtolower(pathinfo($relativePath, PATHINFO_EXTENSION));
        $mimeType = Storage::disk('public')->mimeType($relativePath) ?: 'application/octet-stream';
        $fileSize = Storage::disk('public')->size($relativePath);

        return view('lessons.document-preview', [
            'lesson' => $lesson,
            'previewTitle' => $title,
            'streamUrl' => $streamUrl,
            'downloadUrl' => $downloadUrl,
            'downloadLabel' => $downloadLabel,
            'fileName' => basename($relativePath),
            'fileExtension' => strtoupper($extension),
            'fileSize' => $this->formatPreviewFileSize($fileSize),
            'mimeType' => $mimeType,
            'canInlinePreview' => $this->supportsInlinePreview($mimeType, $extension),
        ]);
    }

    private function streamDocumentFile(string $relativePath, string $fileName, bool $download = false)
    {
        $headers = [
            'Content-Type' => Storage::disk('public')->mimeType($relativePath) ?: 'application/octet-stream',
            'Content-Disposition' => $this->contentDispositionHeader($download ? 'attachment' : 'inline', $fileName),
            'X-Content-Type-Options' => 'nosniff',
        ];

        return response()->file(Storage::disk('public')->path($relativePath), $headers);
    }

    private function supportsInlinePreview(string $mimeType, string $extension): bool
    {
        if ($mimeType === 'application/pdf') {
            return true;
        }

        if (str_starts_with($mimeType, 'image/') || str_starts_with($mimeType, 'text/')) {
            return true;
        }

        return in_array($extension, ['pdf', 'txt', 'csv', 'json', 'xml', 'md', 'png', 'jpg', 'jpeg', 'gif', 'webp', 'svg'], true);
    }

    private function formatPreviewFileSize(int $bytes): string
    {
        if ($bytes < 1024 * 1024) {
            return number_format($bytes / 1024, 1) . ' KB';
        }

        return number_format($bytes / (1024 * 1024), 1) . ' MB';
    }

    private function contentDispositionHeader(string $disposition, string $fileName): string
    {
        $safeName = str_replace(['\\', '"'], ['\\\\', '\"'], $fileName);

        return sprintf('%s; filename="%s"', $disposition, $safeName);
    }
}
