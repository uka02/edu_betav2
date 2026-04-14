<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Lesson;
use App\Models\LessonProgress;
use App\Models\User;
use App\Services\LessonProgressService;
use Carbon\CarbonImmutable;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class GoogleAuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route($this->authenticatedRedirectRoute(Auth::user()));
        }

        return view('auth.login');
    }

    public function showSignup()
    {
        if (Auth::check()) {
            return redirect()->route($this->authenticatedRedirectRoute(Auth::user()));
        }

        return view('auth.signup');
    }

    public function showSignupChoice()
    {
        if (Auth::check()) {
            return redirect()->route($this->authenticatedRedirectRoute(Auth::user()));
        }

        return view('auth.signup-choice');
    }

    public function showEducatorSignup()
    {
        if (Auth::check()) {
            return redirect()->route($this->authenticatedRedirectRoute(Auth::user()));
        }

        return view('auth.signup-educator');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login' => ['required', 'string'],
            'password' => ['required'],
        ]);

        $loginField = filter_var($credentials['login'], FILTER_VALIDATE_EMAIL)
            ? 'email'
            : 'username';

        if (! Auth::attempt([
            $loginField => $credentials['login'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'))) {
            throw ValidationException::withMessages([
                'login' => __('auth.invalid_credentials'),
            ]);
        }

        $request->session()->regenerate();

        return redirect()->intended(route($this->authenticatedRedirectRoute($request->user())));
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8', 'confirmed'],
            'role' => ['required', Rule::in(User::availableRoles())],
            'terms' => ['accepted'],
        ]);

        $user = User::create([
            'name' => $validated['first_name'] . ' ' . $validated['last_name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        Auth::login($user);

        return redirect()->route('dashboard');
    }

    public function redirectToGoogle(Request $request)
    {
        $validated = $request->validate([
            'context' => ['nullable', Rule::in(['login', 'signup'])],
            'role' => ['nullable', Rule::in(User::availableRoles())],
        ]);

        $context = $validated['context'] ?? 'login';
        $role = $context === 'signup'
            ? ($validated['role'] ?? User::ROLE_LEARNER)
            : null;

        $request->session()->put('google_auth_context', [
            'context' => $context,
            'role' => $role,
        ]);

        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(Request $request)
    {
        try {
            $googleAuthContext = $request->session()->pull('google_auth_context', []);
            $isSignupFlow = ($googleAuthContext['context'] ?? 'login') === 'signup';
            $signupRole = ($googleAuthContext['context'] ?? null) === 'signup'
                ? ($googleAuthContext['role'] ?? User::ROLE_LEARNER)
                : User::ROLE_LEARNER;

            $googleUser = Socialite::driver('google')->user();
            $googleId = $googleUser->getId();
            $email = $googleUser->getEmail();

            if (blank($googleId) || blank($email)) {
                throw new Exception(__('auth.google_account_data_missing'));
            }

            $user = User::query()
                ->where('google_id', $googleId)
                ->orWhere('email', $email)
                ->first();

            if (! $user) {
                if (! $isSignupFlow) {
                    return redirect()->route('login')
                        ->with('error', __('auth.account_not_found_signup_first'));
                }

                $user = new User([
                    'email' => $email,
                    'role' => $signupRole,
                    'password' => Hash::make(Str::random(32)),
                ]);
            }

            $user->fill([
                'name' => $googleUser->getName() ?: $user->name,
                'email' => $email,
                'google_id' => $googleId,
                'avatar' => $googleUser->getAvatar(),
            ]);
            $user->role ??= $signupRole;
            $user->email_verified_at ??= now();
            $user->save();

            Auth::login($user, true);
            $request->session()->regenerate();

            return redirect()->route($this->authenticatedRedirectRoute($user))
                ->with('success', __('auth.google_welcome', ['name' => $user->name]));
        } catch (Exception $e) {
            Log::warning('Google authentication failed.', [
                'message' => $e->getMessage(),
            ]);

            return redirect()->route('login')
                ->with('error', __('auth.google_sign_in_failed'));
        }
    }

    public function dashboard(Request $request, LessonProgressService $lessonProgressService)
    {
        $user = Auth::user();

        if ($user->isAdmin()) {
            return redirect()->route('admin.certificate-verifications.index');
        }

        $isEducatorDashboard = $user->isEducator();
        $lessonSearch = trim((string) $request->query('lesson_search', ''));
        $isGlobalLessonSearch = $lessonSearch !== '';
        $featuredLessons = ($isGlobalLessonSearch
                ? Lesson::query()->published()
                : $user->lessons()->published())
            ->select($this->dashboardLessonColumns())
            ->with('user:id,name')
            ->when($lessonSearch !== '', fn ($query) => $this->applyLessonSearch($query, $lessonSearch))
            ->latest()
            ->take($isGlobalLessonSearch ? 6 : 3)
            ->get();

        $continueLearningLessons = $isEducatorDashboard
            ? collect()
            : $this->getContinueLearningLessons($user, $lessonProgressService);
        $educatorActivityLessons = $isEducatorDashboard
            ? $this->getEducatorActivityLessons($user)
            : collect();
        $dashboardMetrics = $isEducatorDashboard
            ? $this->getEducatorDashboardMetrics($user)
            : $this->getLearnerDashboardMetrics($user);

        $trendingLessons = Lesson::published()
            ->select($this->dashboardLessonColumns())
            ->with('user:id,name')
            ->where('user_id', '!=', $user->id)
            ->when($lessonSearch !== '', fn ($query) => $this->applyLessonSearch($query, $lessonSearch))
            ->latest()
            ->take(6)
            ->get();

        return view('auth.dashboard', [
            'isEducatorDashboard' => $isEducatorDashboard,
            'featuredLessons' => $featuredLessons,
            'trendingLessons' => $trendingLessons,
            'continueLearningLessons' => $continueLearningLessons,
            'educatorActivityLessons' => $educatorActivityLessons,
            'lessonSearch' => $lessonSearch,
            'isGlobalLessonSearch' => $isGlobalLessonSearch,
            ...$dashboardMetrics,
        ]);
    }

    private function applyLessonSearch($query, string $lessonSearch)
    {
        $query->where(function ($lessonQuery) use ($lessonSearch) {
            $lessonQuery
                ->where('title', 'like', '%' . $lessonSearch . '%')
                ->orWhere('subject', 'like', '%' . $lessonSearch . '%')
                ->orWhere('type', 'like', '%' . $lessonSearch . '%')
                ->orWhere('difficulty', 'like', '%' . $lessonSearch . '%')
                ->orWhereHas('user', function ($userQuery) use ($lessonSearch) {
                    $userQuery->where('name', 'like', '%' . $lessonSearch . '%');
                });
        });

        return $query;
    }

    private function getLearnerDashboardMetrics(User $user): array
    {
        $learningProgressQuery = $user->lessonProgress()
            ->whereHas('lesson', function ($query) use ($user) {
                $query->where('is_published', true)
                    ->orWhere('user_id', $user->id);
            });

        $learningProgressSummary = (clone $learningProgressQuery)
            ->selectRaw('COALESCE(SUM(watched_seconds), 0) as total_watched_seconds')
            ->selectRaw('COALESCE(AVG(progress_percent), 0) as average_progress_percent')
            ->first();

        $learningActivityDates = (clone $learningProgressQuery)
            ->selectRaw('DATE(COALESCE(last_viewed_at, updated_at)) as activity_date')
            ->distinct()
            ->orderByDesc('activity_date')
            ->get()
            ->pluck('activity_date')
            ->filter()
            ->map(fn (string $date) => CarbonImmutable::parse($date))
            ->values();

        $coursesEnrolledCount = (clone $learningProgressQuery)->count();
        $lessonsCompletedCount = (clone $learningProgressQuery)
            ->where(function ($query) {
                $query->whereNotNull('completed_at')
                    ->orWhere('progress_percent', '>=', 100);
            })
            ->count();
        $totalLearningTimeSeconds = (int) ($learningProgressSummary->total_watched_seconds ?? 0);
        $progressPercentage = (int) round((float) ($learningProgressSummary->average_progress_percent ?? 0));

        return [
            // The dashboard view reuses these two stat slots for learner and educator cards.
            'totalLessonsCreated' => $lessonsCompletedCount,
            'publishedLessons' => $coursesEnrolledCount,
            'certificateCount' => $user->certificates()->count(),
            'totalLearningHours' => $this->calculateLearningHours($totalLearningTimeSeconds),
            'totalLearningHoursDisplay' => $this->formatLearningHours($totalLearningTimeSeconds),
            'progressPercentage' => $progressPercentage,
            'dailyStreak' => $this->calculateDailyStreak($learningActivityDates),
            'activeLearnersCount' => 0,
            'learnersReachedCount' => 0,
            'issuedCertificatesCount' => 0,
        ];
    }

    private function getEducatorDashboardMetrics(User $user): array
    {
        $lessonStats = $user->lessons()
            ->selectRaw('COUNT(*) as total_lessons_created')
            ->selectRaw('COALESCE(SUM(CASE WHEN is_published = 1 THEN 1 ELSE 0 END), 0) as published_lessons')
            ->first();

        $learnerProgressQuery = LessonProgress::query()
            ->where('user_id', '!=', $user->id)
            ->whereHas('lesson', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            });

        $recentLearnerThreshold = now()->subDays(7);
        $learnerProgressSummary = (clone $learnerProgressQuery)
            ->selectRaw('COALESCE(AVG(progress_percent), 0) as average_progress_percent')
            ->selectRaw('COUNT(DISTINCT user_id) as learners_reached_count')
            ->first();

        $totalLessonsCreated = (int) ($lessonStats->total_lessons_created ?? 0);
        $publishedLessons = (int) ($lessonStats->published_lessons ?? 0);
        $learnersReachedCount = (int) ($learnerProgressSummary->learners_reached_count ?? 0);
        $activeLearnersCount = (clone $learnerProgressQuery)
            ->where('last_viewed_at', '>=', $recentLearnerThreshold)
            ->distinct('user_id')
            ->count('user_id');
        $issuedCertificatesCount = Certificate::query()
            ->whereHas('lesson', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })
            ->count();
        $progressPercentage = (int) round((float) ($learnerProgressSummary->average_progress_percent ?? 0));

        return [
            'totalLessonsCreated' => $totalLessonsCreated,
            'publishedLessons' => $publishedLessons,
            'certificateCount' => $issuedCertificatesCount,
            'totalLearningHours' => 0,
            'totalLearningHoursDisplay' => '0',
            'progressPercentage' => $progressPercentage,
            'dailyStreak' => 0,
            'activeLearnersCount' => $activeLearnersCount,
            'learnersReachedCount' => $learnersReachedCount,
            'issuedCertificatesCount' => $issuedCertificatesCount,
        ];
    }

    private function calculateLearningHours(int $totalLearningTimeSeconds): float
    {
        return round($totalLearningTimeSeconds / 3600, 1);
    }

    private function formatLearningHours(int $totalLearningTimeSeconds): string
    {
        $totalLearningHours = $this->calculateLearningHours($totalLearningTimeSeconds);

        if ($totalLearningHours === floor($totalLearningHours)) {
            return (string) (int) $totalLearningHours;
        }

        return number_format($totalLearningHours, 1, '.', '');
    }

    private function calculateDailyStreak(Collection $lessonDates): int
    {
        if ($lessonDates->isEmpty()) {
            return 0;
        }

        $today = now()->startOfDay()->toImmutable();
        $latestLessonDate = $lessonDates->first();

        if ($latestLessonDate->lt($today->subDay())) {
            return 0;
        }

        $streak = 0;
        $currentDate = $latestLessonDate->equalTo($today) ? $today : $today->subDay();

        foreach ($lessonDates as $lessonDate) {
            if (! $lessonDate->equalTo($currentDate)) {
                break;
            }

            $streak++;
            $currentDate = $currentDate->subDay();
        }

        return $streak;
    }

    private function getContinueLearningLessons(User $user, LessonProgressService $lessonProgressService)
    {
        return $user->lessonProgress()
            ->with('lesson.user')
            ->whereHas('lesson', function ($query) use ($user) {
                $query->where('is_published', true)
                    ->orWhere('user_id', $user->id);
            })
            ->orderByDesc('last_viewed_at')
            ->take(6)
            ->get()
            ->map(function ($progress) use ($lessonProgressService) {
                if (! $progress->lesson) {
                    return null;
                }

                $progress = $lessonProgressService->syncStoredProgress($progress, $progress->lesson);

                $progress->lesson->setAttribute('progress_percent', $progress->progress_percent);
                $progress->lesson->setAttribute('last_position_seconds', $progress->last_position_seconds);
                $progress->lesson->setAttribute('watched_seconds', $progress->watched_seconds);

                return $progress->lesson;
            })
            ->filter()
            ->values();
    }

    private function getEducatorActivityLessons(User $user)
    {
        return $user->lessons()
            ->select($this->dashboardLessonColumns())
            ->withCount([
                'progressRecords as learner_count' => function ($query) use ($user) {
                    $query->where('user_id', '!=', $user->id);
                },
                'certificates as issued_certificate_count' => function ($query) {
                    $query->whereNotNull('id');
                },
            ])
            ->withAvg([
                'progressRecords as average_progress_percent' => function ($query) use ($user) {
                    $query->where('user_id', '!=', $user->id);
                },
            ], 'progress_percent')
            ->latest()
            ->take(6)
            ->get()
            ->map(function ($lesson) {
                $lesson->setAttribute('learner_count', (int) ($lesson->learner_count ?? 0));
                $lesson->setAttribute('issued_certificate_count', (int) ($lesson->issued_certificate_count ?? 0));
                $lesson->setAttribute(
                    'average_progress_percent',
                    (int) round((float) ($lesson->average_progress_percent ?? 0))
                );

                return $lesson;
            });
    }

    private function dashboardLessonColumns(): array
    {
        return [
            'id',
            'user_id',
            'title',
            'slug',
            'type',
            'thumbnail',
            'duration_minutes',
            'difficulty',
            'is_free',
            'is_published',
            'created_at',
            'updated_at',
        ];
    }

    public function logout(Request $request)
    {
        $locale = $request->session()->get('locale');

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        if ($locale) {
            $request->session()->put('locale', $locale);
        }

        return redirect()->route('home')->with('success', __('auth.logged_out'));
    }

    private function authenticatedRedirectRoute(User $user): string
    {
        return $user->isAdmin()
            ? 'admin.certificate-verifications.index'
            : 'dashboard';
    }
}
