<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Lesson;
use App\Models\LessonExamAttempt;
use App\Models\LessonProgress;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class HomeController extends Controller
{
    public function index()
    {
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('home', $this->getPublicMetrics());
    }

    private function getPublicMetrics(): array
    {
        if (app()->environment('testing')) {
            return $this->buildPublicMetrics();
        }

        return Cache::remember(
            'home.public_metrics.v1',
            now()->addSeconds(60),
            fn () => $this->buildPublicMetrics(),
        );
    }

    private function buildPublicMetrics(): array
    {
        return [
            'userCount' => User::count(),
            'courseCount' => Lesson::published()->count(),
            'certificateCount' => Certificate::count(),
            'satisfactionLevel' => $this->calculateSatisfactionLevel(),
        ];
    }

    private function calculateSatisfactionLevel(): ?int
    {
        $progressSummary = LessonProgress::query()
            ->selectRaw('COUNT(*) as total_progress_records')
            ->selectRaw('COALESCE(SUM(CASE WHEN completed_at IS NOT NULL OR progress_percent >= 100 THEN 1 ELSE 0 END), 0) as completed_progress_records')
            ->first();

        $attemptSummary = LessonExamAttempt::query()
            ->selectRaw('COUNT(*) as total_exam_attempts')
            ->selectRaw('COALESCE(SUM(CASE WHEN passed = 1 THEN 1 ELSE 0 END), 0) as passed_exam_attempts')
            ->first();

        $rates = collect();
        $totalProgressRecords = (int) ($progressSummary->total_progress_records ?? 0);
        $completedProgressRecords = (int) ($progressSummary->completed_progress_records ?? 0);
        $totalExamAttempts = (int) ($attemptSummary->total_exam_attempts ?? 0);
        $passedExamAttempts = (int) ($attemptSummary->passed_exam_attempts ?? 0);

        if ($totalProgressRecords > 0) {
            $rates->push(($completedProgressRecords / $totalProgressRecords) * 100);
        }

        if ($totalExamAttempts > 0) {
            $rates->push(($passedExamAttempts / $totalExamAttempts) * 100);
        }

        if ($rates->isEmpty()) {
            return null;
        }

        return (int) round($rates->avg());
    }
}
