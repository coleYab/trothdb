<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Problem;
use App\Models\Submission;
use App\Models\Track;
use App\Models\UserLessonProgress;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $user = $request->user();

        $problems = Problem::query()
            ->orderBy('number')
            ->get()
            ->map(fn (Problem $problem) => $problem->toSummary())
            ->values();

        $solvedProblemIds = Submission::query()
            ->where('user_id', $user->id)
            ->where('status', 'accepted')
            ->distinct()
            ->pluck('problem_id')
            ->values()
            ->all();

        $submissions = Submission::query()
            ->where('user_id', $user->id)
            ->with('problem:id,title,number')
            ->latest()
            ->get();

        $accepted = $submissions->where('status', 'accepted');
        $points = $accepted
            ->groupBy('problem_id')
            ->sum(fn ($group) => (float) ($group->max('score') ?? 100));

        $solvedSet = array_flip($solvedProblemIds);
        $unsolved = $problems->reject(fn ($problem) => isset($solvedSet[$problem['id']]))->values();
        $recommended = ($unsolved->isNotEmpty() ? $unsolved : $problems)->take(3)->values();

        $featured = $problems->isNotEmpty()
            ? $problems->get((int) now()->dayOfYear % $problems->count())
            : null;

        $recent = $submissions->take(5)->map(fn (Submission $submission) => [
            'id' => $submission->id,
            'problem_id' => $submission->problem_id,
            'title' => $submission->problem?->title ?? $submission->problem_id,
            'status' => $submission->status,
            'score' => $submission->score,
            'language' => $submission->language,
            'created_at' => $submission->created_at?->toIso8601String(),
        ])->values()->all();

        $track = Track::query()
            ->with(['lessons' => fn ($query) => $query->orderBy('order')])
            ->orderBy('order')
            ->first();

        $continueLesson = null;
        $trackProgress = null;

        if ($track) {
            $completedIds = UserLessonProgress::query()
                ->where('user_id', $user->id)
                ->where('completed', true)
                ->pluck('lesson_id')
                ->all();

            $lessons = $track->lessons;
            $nextLesson = $lessons->first(fn (Lesson $lesson) => ! in_array($lesson->id, $completedIds, true))
                ?? $lessons->first();

            $continueLesson = $nextLesson ? [
                'id' => $nextLesson->id,
                'track' => $track->id,
                'title' => $nextLesson->title,
                'summary' => $nextLesson->summary,
                'minutes' => $nextLesson->minutes,
            ] : null;

            $trackProgress = [
                'title' => $track->title,
                'level' => $track->level,
                'completed' => count($completedIds),
                'total' => $lessons->count(),
            ];
        }

        return Inertia::render('dashboard', [
            'solvedProblemIds' => $solvedProblemIds,
            'stats' => [
                'total_problems' => $problems->count(),
                'solved' => count($solvedProblemIds),
                'submissions' => $submissions->count(),
                'accepted' => $accepted->count(),
                'accuracy' => $submissions->count() > 0
                    ? round($accepted->count() / $submissions->count() * 100)
                    : 0,
                'points' => (int) round($points),
                'streak' => $this->streak($submissions),
            ],
            'featured' => $featured,
            'recommended' => $recommended,
            'recent' => $recent,
            'continueLesson' => $continueLesson,
            'trackProgress' => $trackProgress,
        ]);
    }

    /**
     * Count consecutive days with at least one submission, ending today or yesterday.
     *
     * `Date::use(CarbonImmutable::class)` is enabled in AppServiceProvider, so
     * every modifier returns a new instance and must be reassigned.
     */
    private function streak($submissions): int
    {
        $days = $submissions
            ->map(fn (Submission $submission) => $submission->created_at?->toDateString())
            ->filter()
            ->unique()
            ->sortDesc()
            ->values();

        if ($days->isEmpty()) {
            return 0;
        }

        $cursor = now()->startOfDay();
        if ($days->first() !== $cursor->toDateString()) {
            $cursor = $cursor->subDay();
            if ($days->first() !== $cursor->toDateString()) {
                return 0;
            }
        }

        $lookup = array_flip($days->all());
        $streak = 0;

        while (isset($lookup[$cursor->toDateString()])) {
            $streak++;
            $cursor = $cursor->subDay();
        }

        return $streak;
    }
}
