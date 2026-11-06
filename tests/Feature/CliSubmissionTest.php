<?php

namespace Tests\Feature;

use App\Models\CliToken;
use App\Models\Problem;
use App\Models\Submission;
use App\Models\User;
use Database\Seeders\ProblemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CliSubmissionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProblemSeeder::class);
        Storage::fake('local');
    }

    public function test_unauthenticated_request_is_rejected(): void
    {
        $response = $this->postJson(route('api.judge.problems.submit', 'two-sum'), [
            'score' => 100,
            'code' => 'int main() {}',
        ]);

        $response->assertUnauthorized();
    }

    public function test_submitting_nonexistent_problem_returns_404(): void
    {
        $user = User::factory()->create();
        $token = CliToken::generate($user, 'TestCLI')->token;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson(route('api.judge.problems.submit', 'unknown-problem-123'), [
                'score' => 100,
                'code' => 'int main() {}',
            ]);

        $response->assertNotFound();
    }

    public function test_score_validation_bounds(): void
    {
        $user = User::factory()->create();
        $token = CliToken::generate($user, 'TestCLI')->token;

        // Score above 100
        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson(route('api.judge.problems.submit', 'two-sum'), [
                'score' => 105,
                'code' => 'int main() {}',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('score');

        // Score below 0
        $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson(route('api.judge.problems.submit', 'two-sum'), [
                'score' => -5,
                'code' => 'int main() {}',
            ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors('score');
    }

    public function test_submitting_with_code_string_and_full_score_marks_problem_solved(): void
    {
        $user = User::factory()->create();
        $token = CliToken::generate($user, 'LocalJudge v1.0')->token;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson(route('api.judge.problems.submit', 'two-sum'), [
                'score' => 100,
                'language' => 'cpp',
                'code' => '#include <vector>\nusing namespace std;\nclass Solution { public: vector<int> twoSum() { return {0,1}; } };',
                'runtime_ms' => 4,
                'memory_mb' => 14.5,
                'test_results' => [
                    ['name' => 'Sample 1', 'status' => 'passed', 'runtime_ms' => 2],
                    ['name' => 'Sample 2', 'status' => 'passed', 'runtime_ms' => 2],
                ],
            ]);

        $response->assertCreated()
            ->assertJsonPath('is_solved', true)
            ->assertJsonPath('score', 100)
            ->assertJsonPath('submission.status', 'accepted')
            ->assertJsonPath('submission.problem_id', 'two-sum')
            ->assertJsonPath('submission.language', 'cpp');

        $this->assertDatabaseHas('submissions', [
            'user_id' => $user->id,
            'problem_id' => 'two-sum',
            'score' => 100,
            'status' => 'accepted',
        ]);
    }

    public function test_submitting_partial_score(): void
    {
        $user = User::factory()->create();
        $token = CliToken::generate($user, 'LocalJudge')->token;

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->postJson(route('api.cli.submit'), [
                'problem' => 'ring-buffer',
                'score' => 75.5,
                'language' => 'cpp',
                'code' => '// partial implementation',
                'runtime_ms' => 8,
            ]);

        $response->assertCreated()
            ->assertJsonPath('is_solved', false)
            ->assertJsonPath('score', 75.5)
            ->assertJsonPath('submission.status', 'wrong_answer');

        $this->assertDatabaseHas('submissions', [
            'user_id' => $user->id,
            'problem_id' => 'ring-buffer',
            'score' => 75.5,
            'status' => 'wrong_answer',
        ]);
    }

    public function test_submitting_source_code_file_upload(): void
    {
        $user = User::factory()->create();
        $token = CliToken::generate($user, 'LocalJudge v1.0')->token;

        $sourceContent = "#include <iostream>\nint main() { std::cout << \"solved\"; return 0; }";
        $file = UploadedFile::fake()->createWithContent('solution_fast.cpp', $sourceContent);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->post(route('api.judge.problems.submit', 'lru-cache'), [
                'score' => 100,
                'file' => $file,
                'runtime_ms' => 3,
                'memory_mb' => 12.8,
            ]);

        $response->assertCreated()
            ->assertJsonPath('is_solved', true)
            ->assertJsonPath('score', 100)
            ->assertJsonPath('submission.source_file_name', 'solution_fast.cpp')
            ->assertJsonPath('submission.language', 'cpp');

        $submission = Submission::where('problem_id', 'lru-cache')
            ->where('user_id', $user->id)
            ->first();

        $this->assertNotNull($submission);
        $this->assertEquals('solution_fast.cpp', $submission->source_file_name);
        $this->assertEquals($sourceContent, $submission->code);
    }

    public function test_submitting_cpp_header_or_source_file(): void
    {
        $user = User::factory()->create();
        $token = CliToken::generate($user, 'LocalJudge')->token;

        $cppContent = "#include <vector>\nstd::vector<int> twoSum(std::vector<int>& n, int t) { return {0, 1}; }\n";
        $file = UploadedFile::fake()->createWithContent('solution.hpp', $cppContent);

        $response = $this->withHeader('Authorization', "Bearer {$token}")
            ->post(route('api.cli.problems.submit', 'two-sum'), [
                'score' => 100,
                'source_code' => $file,
            ]);

        $response->assertCreated()
            ->assertJsonPath('submission.language', 'cpp')
            ->assertJsonPath('submission.source_file_name', 'solution.hpp');
    }
}
