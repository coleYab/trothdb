<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Problem;
use App\Models\Submission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class CliSubmissionController extends Controller
{
    /**
     * Extension to language map (Strictly C++ platform).
     */
    private const EXTENSION_MAP = [
        'cpp' => 'cpp',
        'cc' => 'cpp',
        'cxx' => 'cpp',
        'hpp' => 'cpp',
        'h' => 'cpp',
        'c' => 'cpp',
    ];

    /**
     * Submit code solution from CLI / LocalJudge with score and source code file.
     */
    public function submit(Request $request, ?string $problem = null): JsonResponse
    {
        $user = $request->user();
        if (! $user) {
            return response()->json([
                'message' => 'Unauthenticated. Provide a valid token with Authorization: Bearer arena_live_... or log in with arena auth login.',
            ], 401);
        }

        // 1. Resolve target problem
        $problemIdentifier = $problem ?? $request->input('problem') ?? $request->input('problem_id') ?? $request->input('problem_slug');
        if (! $problemIdentifier) {
            return response()->json([
                'message' => 'Problem identifier missing. Specify problem in URL or provide problem / problem_id in request body.',
            ], 422);
        }

        $targetProblem = Problem::query()
            ->where('id', $problemIdentifier)
            ->orWhere('number', $problemIdentifier)
            ->first();

        if (! $targetProblem) {
            return response()->json([
                'message' => "Problem '{$problemIdentifier}' not found in the problemset.",
            ], 404);
        }

        // 2. Validate input fields
        $validated = $request->validate([
            'score' => ['required', 'numeric', 'min:0', 'max:100'],
            'language' => ['nullable', 'string', 'max:32'],
            'status' => ['nullable', 'string', 'in:accepted,wrong_answer,time_limit,memory_limit,runtime_error,compile_error,partial'],
            'runtime_ms' => ['nullable', 'integer', 'min:0'],
            'memory_mb' => ['nullable', 'numeric', 'min:0'],
            'test_results' => ['nullable'],
            'stdout' => ['nullable', 'string'],
            'stderr' => ['nullable', 'string'],
            'file' => ['nullable', 'file', 'max:10240'],
            'source_code' => ['nullable'],
            'code' => ['nullable', 'string'],
        ]);

        $score = round((float) $validated['score'], 2);

        // 3. Process source code as a string
        $file = $request->file('file') ?? $request->file('source_code') ?? $request->file('code');
        $sourceFileName = null;
        $detectedLanguage = null;

        if ($file && $file->isValid()) {
            $sourceFileName = $file->getClientOriginalName();
            $extension = strtolower($file->getClientOriginalExtension());
            $detectedLanguage = self::EXTENSION_MAP[$extension] ?? null;

            // Read the source code string from the uploaded file
            $code = file_get_contents($file->getRealPath());
        } else {
            // Text source code payload provided directly as string
            $code = $request->input('code') ?? $request->input('source_code');
            $sourceFileName = $request->input('file_name');

            if (! is_string($code) || trim($code) === '') {
                throw ValidationException::withMessages([
                    'code' => ['Either an uploaded source code file (file/source_code) or code string is required.'],
                ]);
            }
        }

        $language = 'cpp';

        // 4. Determine submission status
        if (! empty($validated['status'])) {
            $status = $validated['status'];
        } else {
            if ($score >= 100) {
                $status = 'accepted';
            } elseif ($score <= 0) {
                $status = 'wrong_answer';
            } else {
                $status = 'wrong_answer';
            }
        }

        // Parse test results if stringified JSON was sent
        $testResults = $validated['test_results'] ?? null;
        if (is_string($testResults)) {
            $decoded = json_decode($testResults, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $testResults = $decoded;
            }
        }

        // 5. Store submission record with code string
        $submission = Submission::create([
            'user_id' => $user->id,
            'problem_id' => $targetProblem->id,
            'language' => $language,
            'code' => $code,
            'score' => $score,
            'status' => $status,
            'source_file_name' => $sourceFileName,
            'runtime_ms' => $validated['runtime_ms'] ?? null,
            'memory_mb' => isset($validated['memory_mb']) ? round((float) $validated['memory_mb'], 2) : null,
            'test_results' => $testResults,
            'stdout' => $validated['stdout'] ?? ($score >= 100 ? 'All local judge tests passed.' : "Local judge scored {$score}/100."),
            'stderr' => $validated['stderr'] ?? null,
        ]);

        $isSolved = $status === 'accepted' || $score >= 100;

        return response()->json([
            'message' => $isSolved ? 'CLI submission accepted and problem marked as solved!' : "CLI submission received with score {$score}/100.",
            'is_solved' => $isSolved,
            'score' => $score,
            'submission' => [
                'id' => $submission->id,
                'user_id' => $submission->user_id,
                'problem_id' => $submission->problem_id,
                'language' => $submission->language,
                'code' => $submission->code,
                'score' => $submission->score,
                'status' => $submission->status,
                'source_file_name' => $submission->source_file_name,
                'runtime_ms' => $submission->runtime_ms,
                'memory_mb' => $submission->memory_mb,
                'test_results' => $submission->test_results,
                'stdout' => $submission->stdout,
                'stderr' => $submission->stderr,
                'created_at' => $submission->created_at->toISOString(),
            ],
        ], 201);
    }
}
