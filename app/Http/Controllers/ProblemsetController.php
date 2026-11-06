<?php

namespace App\Http\Controllers;

use App\Models\Problem;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProblemsetController extends Controller
{
    /**
     * Display the list of available problems.
     */
    public function index(): Response
    {
        $problems = Problem::query()
            ->orderBy('number')
            ->get()
            ->map(fn (Problem $p) => $p->toSummary())
            ->values()
            ->all();

        $solvedProblemIds = [];
        if (auth()->check()) {
            $solvedProblemIds = Submission::query()
                ->where('user_id', auth()->id())
                ->where('status', 'accepted')
                ->distinct()
                ->pluck('problem_id')
                ->values()
                ->all();
        }

        return Inertia::render('problems/index', [
            'problems' => $problems,
            'solvedProblemIds' => $solvedProblemIds,
        ]);
    }

    /**
     * Show form to create a new problem.
     */
    public function create(): Response
    {
        $nextNumber = (int) (Problem::max('number') ?? 0) + 1;

        return Inertia::render('problems/create', [
            'nextNumber' => $nextNumber,
        ]);
    }

    /**
     * Store a newly created problem with test data and individual grades.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'id' => ['nullable', 'string', 'max:100', 'regex:/^[a-z0-9\-]+$/', 'unique:problems,id'],
            'difficulty' => ['required', 'string', 'in:Easy,Medium,Hard'],
            'discipline' => ['required', 'string', 'max:50'],
            'topic' => ['nullable', 'string', 'max:64'],
            'summary' => ['required', 'string', 'max:1000'],
            'description' => ['required'],
            'tags' => ['nullable'],
            'constraints' => ['nullable'],
            'follow_up' => ['nullable', 'string'],
            'starter_code' => ['required', 'string'],
            'io_mode' => ['nullable', 'string', 'in:harness,stdio'],
            'total_points' => ['nullable', 'integer', 'min:1'],
            'time_limit_ms' => ['nullable', 'integer', 'min:10'],
            'memory_limit_mb' => ['nullable', 'integer', 'min:16'],
            'judge_solution' => ['nullable', 'string'],
            'judge_test_h' => ['nullable', 'string'],
            'hints' => ['nullable'],
            'examples' => ['nullable', 'array'],
            'examples.*.input' => ['nullable', 'string'],
            'examples.*.output' => ['nullable', 'string'],
            'examples.*.explanation' => ['nullable', 'string'],
            'test_cases' => ['required', 'array', 'min:1'],
            'test_cases.*.case_id' => ['nullable', 'string', 'max:64'],
            'test_cases.*.name' => ['required', 'string'],
            'test_cases.*.group' => ['nullable', 'string', 'max:32'],
            'test_cases.*.file_path' => ['nullable', 'string', 'max:255'],
            'test_cases.*.test_code_cpp' => ['nullable', 'string'],
            'test_cases.*.time_limit_ms' => ['nullable', 'integer', 'min:10'],
            'test_cases.*.input' => ['nullable', 'string'],
            'test_cases.*.expected' => ['nullable', 'string'],
            'test_cases.*.grade' => ['required', 'numeric', 'min:0', 'max:100'],
            'test_cases.*.hidden' => ['nullable', 'boolean'],
            'time_limit' => ['nullable', 'string'],
            'memory_limit' => ['nullable', 'string'],
            'editorial_insight' => ['nullable', 'string'],
            'editorial_time' => ['nullable', 'string'],
            'editorial_space' => ['nullable', 'string'],
            'editorial_steps' => ['nullable'],
        ]);

        $slug = ! empty($validated['id']) ? $validated['id'] : Str::slug($validated['title']);
        if (Problem::where('id', $slug)->exists()) {
            $slug .= '-' . (Problem::count() + 1);
        }

        $nextNumber = (int) (Problem::max('number') ?? 0) + 1;

        // Process description paragraphs
        $description = is_array($validated['description'])
            ? $validated['description']
            : array_values(array_filter(array_map('trim', explode("\n\n", (string) $validated['description']))));

        // Process tags
        $tags = [];
        if (! empty($validated['tags'])) {
            $tags = is_array($validated['tags'])
                ? $validated['tags']
                : array_values(array_filter(array_map('trim', explode(',', (string) $validated['tags']))));
        }
        if (empty($tags)) {
            $tags = [$validated['discipline']];
        }

        // Process constraints
        $constraints = [];
        if (! empty($validated['constraints'])) {
            $constraints = is_array($validated['constraints'])
                ? $validated['constraints']
                : array_values(array_filter(array_map('trim', explode("\n", (string) $validated['constraints']))));
        }

        // Process hints
        $hints = [];
        if (! empty($validated['hints'])) {
            $hints = is_array($validated['hints'])
                ? $validated['hints']
                : array_values(array_filter(array_map('trim', explode("\n", (string) $validated['hints']))));
        }

        // Process test cases with individual grades
        $sampleTests = array_map(function ($tc, $idx) {
            return [
                'name' => $tc['name'] ?: 'Test Case #' . ($idx + 1),
                'input' => $tc['input'],
                'expected' => $tc['expected'],
                'grade' => (float) ($tc['grade'] ?? 10.0),
                'hidden' => (bool) ($tc['hidden'] ?? false),
            ];
        }, $validated['test_cases'], array_keys($validated['test_cases']));

        // Editorial structure
        $editorialSteps = [];
        if (! empty($validated['editorial_steps'])) {
            $editorialSteps = is_array($validated['editorial_steps'])
                ? $validated['editorial_steps']
                : array_values(array_filter(array_map('trim', explode("\n", (string) $validated['editorial_steps']))));
        }

        $editorial = [
            'insight' => $validated['editorial_insight'] ?? 'Analyze problem bounds and optimize memory layout.',
            'steps' => ! empty($editorialSteps) ? $editorialSteps : [
                'Understand constraints and edge conditions.',
                'Implement solution using idiomatic C++20 container semantics.',
            ],
            'time_complexity' => $validated['editorial_time'] ?? 'O(n)',
            'space_complexity' => $validated['editorial_space'] ?? 'O(1)',
        ];

        $testing = [
            'judge' => 'Your C++20 submission is compiled with GCC 14 -O2 and executed against each test case with time and memory limit enforcement.',
            'time_limit' => $validated['time_limit'] ?? '2 seconds per test file',
            'memory_limit' => $validated['memory_limit'] ?? '256 MB',
            'languages' => ['C++ 20'],
            'notes' => [
                'Compiled with -std=c++20 -O2 -pipe.',
                'Individual test cases are graded by assigned point weights.',
            ],
            'sample_tests' => $sampleTests,
        ];

        $problem = Problem::create([
            'id' => $slug,
            'number' => $nextNumber,
            'title' => $validated['title'],
            'difficulty' => $validated['difficulty'],
            'discipline' => $validated['discipline'],
            'topic' => $validated['topic'] ?? null,
            'acceptance_rate' => 50.0,
            'tags' => $tags,
            'summary' => $validated['summary'],
            'description' => $description,
            'examples' => $validated['examples'],
            'constraints' => $constraints,
            'follow_up' => $validated['follow_up'] ?? null,
            'starter_code' => [
                'cpp' => $validated['starter_code'],
            ],
            'io_mode' => $validated['io_mode'] ?? 'harness',
            'total_points' => $validated['total_points'] ?? 100,
            'time_limit_ms' => (int) ($validated['time_limit_ms'] ?? 2000),
            'memory_limit_mb' => (int) ($validated['memory_limit_mb'] ?? 256),
            'version' => 1,
            'is_active' => true,
            'hints' => $hints,
            'editorial' => $editorial,
            'testing' => $testing,
        ]);

        // Insert individual C++ test cases (Specification 2.0)
        foreach ($validated['test_cases'] as $idx => $tc) {
            $isSecret = (bool) ($tc['hidden'] ?? false);
            $caseId = $tc['case_id'] ?? ($isSecret ? "h" . ($idx + 1) : "p" . ($idx + 1));
            $folder = $isSecret ? 'hidden' : 'public';
            \App\Models\ProblemTestCase::create([
                'problem_id' => $problem->id,
                'case_id' => $caseId,
                'name' => $tc['name'] ?: 'Test Case #' . ($idx + 1),
                'group' => $tc['group'] ?? 'basic',
                'file_path' => $tc['file_path'] ?? "tests/{$folder}/test_{$caseId}.cpp",
                'test_code_cpp' => $tc['test_code_cpp'] ?? $tc['expected'] ?? "// C++ assertion test\n",
                'points' => (int) ($tc['grade'] ?? 10),
                'time_limit_ms' => (int) ($tc['time_limit_ms'] ?? 2000),
                'is_hidden' => $isSecret,
                'order_index' => $idx,
            ]);
        }

        // Insert judge reference solution asset if provided
        if (! empty($validated['judge_solution'])) {
            \App\Models\ProblemAsset::create([
                'problem_id' => $problem->id,
                'file_path' => 'tests/judge_solution.hpp',
                'file_type' => 'reference_solution',
                'content' => $validated['judge_solution'],
                'sha256_hash' => hash('sha256', (string) $validated['judge_solution']),
                'is_secret' => true,
            ]);
        }

        // Insert assertion framework header if provided
        if (! empty($validated['judge_test_h'])) {
            \App\Models\ProblemAsset::create([
                'problem_id' => $problem->id,
                'file_path' => 'tests/judge_test.h',
                'file_type' => 'framework_header',
                'content' => $validated['judge_test_h'],
                'sha256_hash' => hash('sha256', (string) $validated['judge_test_h']),
                'is_secret' => false,
            ]);
        }

        return redirect()->route('problems.show', $problem->id);
    }

    /**
     * Display a single problem with its full statement and testing details.
     */
    public function show(string $problem): Response
    {
        $match = Problem::query()
            ->with(['testCases', 'assets'])
            ->where('id', $problem)
            ->orWhere('number', $problem)
            ->first();

        abort_if($match === null, 404, 'Problem not found.');

        $isSolved = false;
        $userSubmissions = [];

        if (auth()->check()) {
            $isSolved = Submission::query()
                ->where('user_id', auth()->id())
                ->where('problem_id', $match->id)
                ->where('status', 'accepted')
                ->exists();

            $userSubmissions = Submission::query()
                ->where('user_id', auth()->id())
                ->where('problem_id', $match->id)
                ->latest()
                ->get(['id', 'language', 'status', 'runtime_ms', 'memory_mb', 'created_at', 'code'])
                ->toArray();
        }

        $prevProblem = Problem::query()
            ->where('number', '<', $match->number)
            ->orderBy('number', 'desc')
            ->first(['id', 'number', 'title', 'difficulty']);

        $nextProblem = Problem::query()
            ->where('number', '>', $match->number)
            ->orderBy('number', 'asc')
            ->first(['id', 'number', 'title', 'difficulty']);

        $totalProblems = Problem::count();
        $position = Problem::query()->where('number', '<=', $match->number)->count();

        $submissions = Submission::query()
            ->with(['user:id,name,username'])
            ->where('problem_id', $match->id)
            ->latest()
            ->get()
            ->map(function (Submission $sub) {
                return [
                    'id' => $sub->id,
                    'user_id' => $sub->user_id,
                    'user_name' => $sub->user?->name ?? 'Anonymous Coder',
                    'username' => $sub->user?->username ?? 'user',
                    'language' => $sub->language,
                    'code' => $sub->code,
                    'score' => $sub->score != null ? (float) $sub->score : 100,
                    'created_at' => $sub->created_at?->toISOString() ?? now()->toISOString(),
                ];
            })
            ->values()
            ->all();

        $suiteCases = $match->testCases
            ->map(fn ($case) => [
                'id' => $case->id,
                'case_id' => $case->case_id,
                'name' => $case->name,
                'group' => $case->group,
                'points' => (int) $case->points,
                'time_limit_ms' => (int) $case->time_limit_ms,
                'hidden' => (bool) $case->is_hidden,
                'code' => $case->is_hidden ? null : $case->test_code_cpp,
            ])
            ->values()
            ->all();

        return Inertia::render('problems/show', [
            'problem' => $match->withoutRelations()->toArray(),
            'isSolved' => $isSolved,
            'submissions' => $submissions,
            'suiteCases' => $suiteCases,
            'prevProblem' => $prevProblem ? [
                'id' => $prevProblem->id,
                'number' => $prevProblem->number,
                'title' => $prevProblem->title,
                'difficulty' => $prevProblem->difficulty,
            ] : null,
            'nextProblem' => $nextProblem ? [
                'id' => $nextProblem->id,
                'number' => $nextProblem->number,
                'title' => $nextProblem->title,
                'difficulty' => $nextProblem->difficulty,
            ] : null,
            'position' => $position,
            'totalProblems' => $totalProblems,
            'initialSubmissions' => $userSubmissions,
        ]);
    }
}
