<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Inertia\Inertia;
use Inertia\Response;

class ProblemsetController extends Controller
{
    /**
     * Display the list of available problems.
     */
    public function index(): Response
    {
        $problems = collect($this->problems())
            ->map(fn (array $problem): array => Arr::only($problem, [
                'id',
                'number',
                'title',
                'difficulty',
                'acceptance_rate',
                'tags',
                'summary',
            ]))
            ->values()
            ->all();

        return Inertia::render('problems/index', [
            'problems' => $problems,
        ]);
    }

    /**
     * Display a single problem with its full statement and testing details.
     */
    public function show(string $problem): Response
    {
        $match = collect($this->problems())->first(
            fn (array $item): bool => $item['id'] === $problem || (string) $item['number'] === $problem
        );

        abort_if($match === null, 404, 'Problem not found.');

        return Inertia::render('problems/show', [
            'problem' => $match,
        ]);
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function problems(): array
    {
        return config('problems', []);
    }
}
