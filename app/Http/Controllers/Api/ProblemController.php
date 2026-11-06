<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Problem;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProblemController extends Controller
{
    /**
     * Display a listing of problems / challenges.
     */
    public function index(Request $request): JsonResponse
    {
        $problems = Problem::query()
            ->orderBy('number')
            ->get();

        $solvedIds = [];
        if ($user = $request->user()) {
            $solvedIds = Submission::query()
                ->where('user_id', $user->id)
                ->where('status', 'accepted')
                ->pluck('problem_id')
                ->unique()
                ->toArray();
        }

        $data = $problems->map(function (Problem $problem) use ($solvedIds) {
            $summary = $problem->toSummary();
            $summary['solved'] = in_array($problem->id, $solvedIds, true);

            return $summary;
        });

        return response()->json([
            'data' => $data,
            'total' => $data->count(),
        ]);
    }

    /**
     * Display the specified problem / challenge.
     */
    public function show(Request $request, string $problem): JsonResponse
    {
        $match = Problem::query()
            ->where('id', $problem)
            ->orWhere('number', $problem)
            ->firstOrFail();

        $isSolved = false;
        if ($user = $request->user()) {
            $isSolved = Submission::query()
                ->where('user_id', $user->id)
                ->where('problem_id', $match->id)
                ->where('status', 'accepted')
                ->exists();
        }

        return response()->json([
            'data' => $match,
            'is_solved' => $isSolved,
        ]);
    }

    /**
     * Store a newly created problem via API.
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'id' => ['nullable', 'string', 'max:100', 'unique:problems,id'],
            'difficulty' => ['required', 'string', 'in:Easy,Medium,Hard'],
            'discipline' => ['nullable', 'string', 'max:50'],
            'summary' => ['required', 'string', 'max:1000'],
            'description' => ['required'],
            'tags' => ['nullable'],
            'constraints' => ['nullable'],
            'starter_code' => ['required', 'string'],
            'examples' => ['required', 'array', 'min:1'],
            'test_cases' => ['required', 'array', 'min:1'],
            'test_cases.*.name' => ['required', 'string'],
            'test_cases.*.input' => ['required', 'string'],
            'test_cases.*.expected' => ['required', 'string'],
            'test_cases.*.grade' => ['required', 'numeric', 'min:0', 'max:100'],
        ]);

        $slug = ! empty($validated['id']) ? $validated['id'] : \Illuminate\Support\Str::slug($validated['title']);
        $nextNumber = (int) (Problem::max('number') ?? 0) + 1;

        $description = is_array($validated['description'])
            ? $validated['description']
            : [(string) $validated['description']];

        $sampleTests = array_map(function ($tc, $idx) {
            return [
                'name' => $tc['name'] ?: 'Test Case #' . ($idx + 1),
                'input' => $tc['input'],
                'expected' => $tc['expected'],
                'grade' => (float) ($tc['grade'] ?? 10.0),
                'hidden' => (bool) ($tc['hidden'] ?? false),
            ];
        }, $validated['test_cases'], array_keys($validated['test_cases']));

        $problem = Problem::create([
            'id' => $slug,
            'number' => $nextNumber,
            'title' => $validated['title'],
            'difficulty' => $validated['difficulty'],
            'discipline' => $validated['discipline'] ?? 'Algorithms',
            'acceptance_rate' => 50.0,
            'tags' => is_array($validated['tags'] ?? null) ? $validated['tags'] : ['C++'],
            'summary' => $validated['summary'],
            'description' => $description,
            'examples' => $validated['examples'],
            'constraints' => is_array($validated['constraints'] ?? null) ? $validated['constraints'] : [],
            'starter_code' => [
                'cpp' => $validated['starter_code'],
            ],
            'editorial' => [
                'insight' => 'Analyze problem bounds and memory footprint.',
                'steps' => ['Analyze constraints.', 'Implement solution in C++20.'],
                'time_complexity' => 'O(n)',
                'space_complexity' => 'O(1)',
            ],
            'testing' => [
                'judge' => 'Compiled with GCC 14 -std=c++20 -O2.',
                'time_limit' => '2 seconds per test file',
                'memory_limit' => '256 MB',
                'languages' => ['C++ 20'],
                'notes' => ['Individual test cases are graded by assigned point weights.'],
                'sample_tests' => $sampleTests,
            ],
            'hints' => [],
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
                'file_path' => "tests/{$folder}/test_{$caseId}.cpp",
                'test_code_cpp' => $tc['test_code_cpp'] ?? $tc['expected'] ?? "// Test case\n",
                'points' => (int) ($tc['grade'] ?? 10),
                'time_limit_ms' => (int) ($tc['time_limit_ms'] ?? 2000),
                'is_hidden' => $isSecret,
                'order_index' => $idx,
            ]);
        }

        return response()->json([
            'message' => 'Problem created successfully.',
            'data' => $problem,
        ], 201);
    }

    /**
     * Retrieve problem bundle for LocalJudge CLI (RFC 8628 & Spec 2.0).
     */
    public function bundle(Request $request, string $problem): JsonResponse
    {
        $target = Problem::query()
            ->with(['testCases', 'assets'])
            ->where('id', $problem)
            ->orWhere('number', $problem)
            ->firstOrFail();

        $files = [
            'solution.cpp' => $target->starter_code['cpp'] ?? "// C++ solution\n",
            'SPEC.md' => "# {$target->title}\n\n{$target->summary}\n",
        ];

        foreach ($target->assets as $asset) {
            $files[$asset->file_path] = $asset->content;
        }

        $cases = $target->testCases->map(function ($tc) {
            return [
                'id' => $tc->case_id,
                'name' => $tc->name,
                'file' => $tc->file_path,
                'group' => $tc->group,
                'points' => $tc->points,
                'time_limit_ms' => $tc->time_limit_ms,
                'hidden' => (bool) $tc->is_hidden,
                'code' => $tc->test_code_cpp,
            ];
        })->values()->all();

        $manifestString = json_encode([
            'id' => $target->id,
            'version' => $target->version ?? 1,
            'files' => array_keys($files),
            'cases' => array_column($cases, 'id'),
        ]);

        return response()->json([
            'schema_version' => '2.0',
            'problem_id' => $target->id,
            'title' => $target->title,
            'version' => $target->version ?? 1,
            'languages' => ['cpp'],
            'compiler_flags' => ['-std=c++20', '-O2'],
            'total_points' => $target->total_points ?? 100,
            'files' => $files,
            'cases' => $cases,
            'manifest_sha256' => hash('sha256', (string) $manifestString),
        ]);
    }
}
