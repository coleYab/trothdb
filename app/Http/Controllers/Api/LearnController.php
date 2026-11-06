<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Lesson;
use App\Models\Track;
use App\Models\UserLessonProgress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LearnController extends Controller
{
    /**
     * List all learning tracks with user progress.
     */
    public function tracks(Request $request): JsonResponse
    {
        $tracks = Track::query()
            ->with(['lessons' => fn ($q) => $q->orderBy('order')])
            ->orderBy('order')
            ->get();

        $completedLessonIds = [];
        if ($user = $request->user()) {
            $completedLessonIds = UserLessonProgress::query()
                ->where('user_id', $user->id)
                ->where('completed', true)
                ->pluck('lesson_id')
                ->toArray();
        }

        $data = $tracks->map(function (Track $track) use ($completedLessonIds) {
            $totalLessons = $track->lessons->count();
            $completedLessons = $track->lessons->whereIn('id', $completedLessonIds)->count();
            $progressPercent = $totalLessons > 0 ? (int) round(($completedLessons / $totalLessons) * 100) : 0;

            return [
                'slug' => $track->id,
                'title' => $track->title,
                'level' => $track->level,
                'hours' => $track->hours,
                'summary' => $track->summary,
                'description' => $track->description,
                'progress' => $progressPercent,
                'continue_lesson' => $track->continue_lesson,
                'lessons_count' => $totalLessons,
                'completed_count' => $completedLessons,
            ];
        });

        return response()->json([
            'data' => $data,
        ]);
    }

    /**
     * Show syllabus for a single track.
     */
    public function track(Request $request, string $track): JsonResponse
    {
        $target = Track::query()
            ->with(['lessons' => fn ($q) => $q->orderBy('order')])
            ->where('id', $track)
            ->firstOrFail();

        $completedLessonIds = [];
        if ($user = $request->user()) {
            $completedLessonIds = UserLessonProgress::query()
                ->where('user_id', $user->id)
                ->where('completed', true)
                ->pluck('lesson_id')
                ->toArray();
        }

        // Group lessons into modules
        $modules = $target->lessons
            ->groupBy('module_title')
            ->map(function ($lessons, $moduleTitle) use ($completedLessonIds) {
                return [
                    'title' => $moduleTitle,
                    'summary' => $lessons->first()?->module_summary ?? '',
                    'items' => $lessons->map(function (Lesson $lesson) use ($completedLessonIds) {
                        return [
                            'id' => $lesson->id,
                            'type' => 'lesson',
                            'title' => $lesson->title,
                            'lessonSlug' => $lesson->slug,
                            'minutes' => $lesson->minutes,
                            'completed' => in_array($lesson->id, $completedLessonIds, true),
                        ];
                    })->values(),
                ];
            })->values();

        $total = $target->lessons->count();
        $done = $target->lessons->whereIn('id', $completedLessonIds)->count();

        return response()->json([
            'data' => [
                'slug' => $target->id,
                'title' => $target->title,
                'level' => $target->level,
                'hours' => $target->hours,
                'summary' => $target->summary,
                'description' => $target->description,
                'prerequisites' => $target->prerequisites ?? [],
                'progress' => $total > 0 ? (int) round(($done / $total) * 100) : 0,
                'completed' => $done,
                'total' => $total,
                'modules' => $modules,
            ],
        ]);
    }

    /**
     * Show lesson content.
     */
    public function lesson(Request $request, string $track, string $lesson): JsonResponse
    {
        $targetTrack = Track::query()
            ->where('id', $track)
            ->firstOrFail();

        $targetLesson = Lesson::query()
            ->where('track_id', $targetTrack->id)
            ->where(function ($q) use ($lesson) {
                $q->where('id', $lesson)->orWhere('slug', $lesson);
            })
            ->firstOrFail();

        $isCompleted = false;
        if ($user = $request->user()) {
            $isCompleted = UserLessonProgress::query()
                ->where('user_id', $user->id)
                ->where('lesson_id', $targetLesson->id)
                ->value('completed') ?? false;
        }

        return response()->json([
            'data' => [
                'track' => [
                    'slug' => $targetTrack->id,
                    'title' => $targetTrack->title,
                    'level' => $targetTrack->level,
                    'hours' => $targetTrack->hours,
                ],
                'lesson' => [
                    'slug' => $targetLesson->slug,
                    'title' => $targetLesson->title,
                    'summary' => $targetLesson->summary,
                    'minutes' => $targetLesson->minutes,
                    'blocks' => $targetLesson->blocks,
                    'quiz' => $targetLesson->quiz,
                    'assembly' => $targetLesson->assembly,
                    'discussion' => $targetLesson->discussion ?? [],
                ],
                'completed' => (bool) $isCompleted,
            ],
        ]);
    }

    /**
     * Toggle lesson completion for user.
     */
    public function toggleComplete(Request $request, string $track, string $lesson): JsonResponse
    {
        $targetTrack = Track::query()->where('id', $track)->firstOrFail();
        $targetLesson = Lesson::query()
            ->where('track_id', $targetTrack->id)
            ->where(function ($q) use ($lesson) {
                $q->where('id', $lesson)->orWhere('slug', $lesson);
            })
            ->firstOrFail();

        $userId = $request->user()?->id ?? 1;

        $progress = UserLessonProgress::query()
            ->firstOrNew(['user_id' => $userId, 'lesson_id' => $targetLesson->id]);

        $progress->completed = ! $progress->completed;
        $progress->save();

        return response()->json([
            'completed' => $progress->completed,
            'message' => $progress->completed ? 'Lesson marked complete.' : 'Lesson marked incomplete.',
        ]);
    }
}
