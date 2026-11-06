<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use App\Models\Track;
use Inertia\Inertia;
use Inertia\Response;

class LearnController extends Controller
{
    /**
     * Display every learning track.
     */
    public function index(): Response
    {
        $tracks = Track::query()
            ->with(['lessons' => fn ($q) => $q->orderBy('order')])
            ->orderBy('order')
            ->get();

        return Inertia::render('learn/index', [
            'dbTracks' => $tracks->toArray(),
        ]);
    }

    /**
     * Display a track syllabus.
     */
    public function show(string $track): Response
    {
        $target = Track::query()
            ->with(['lessons' => fn ($q) => $q->orderBy('order')])
            ->where('id', $track)
            ->first();

        abort_if($target === null, 404, 'Track not found.');

        return Inertia::render('learn/show', [
            'track' => $track,
            'dbTrack' => $target->toArray(),
        ]);
    }

    /**
     * Display a single lesson.
     */
    public function lesson(string $track, string $lesson): Response
    {
        $targetTrack = Track::query()->where('id', $track)->first();
        abort_if($targetTrack === null, 404, 'Track not found.');

        $targetLesson = Lesson::query()
            ->where('track_id', $targetTrack->id)
            ->where(function ($q) use ($lesson) {
                $q->where('id', $lesson)->orWhere('slug', $lesson);
            })
            ->first();

        abort_if($targetLesson === null, 404, 'Lesson not found.');

        return Inertia::render('learn/lesson', [
            'track' => $track,
            'lesson' => $lesson,
            'dbLesson' => $targetLesson->toArray(),
        ]);
    }
}
