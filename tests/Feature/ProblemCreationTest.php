<?php

namespace Tests\Feature;

use App\Models\Problem;
use App\Models\User;
use Database\Seeders\ProblemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ProblemCreationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProblemSeeder::class);
    }

    public function test_guests_are_redirected_when_visiting_problem_creation_page(): void
    {
        $response = $this->get(route('problems.create'));
        $response->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_problem_creation_page(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('problems.create'));

        $response->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('problems/create')
                ->has('nextNumber')
            );
    }

    public function test_user_can_create_a_problem_with_individual_test_grades(): void
    {
        $user = User::factory()->create();

        $payload = [
            'title' => 'Cache-Aware Transpose',
            'id' => 'cache-aware-transpose',
            'difficulty' => 'Hard',
            'discipline' => 'Performance',
            'summary' => 'Compute in-place matrix transpose with blocked tiles to optimize cache hit rate.',
            'description' => "Given an N x N matrix of 32-bit floating point numbers, transpose the matrix in place.\n\nYour implementation must use cache-blocking tiling techniques.",
            'tags' => 'Matrix, Cache, Performance',
            'constraints' => "1 <= N <= 8192\nSingle-thread execution\nZero dynamic heap allocations in inner loop",
            'follow_up' => 'Can you use AVX-512 vector register transpositions to double throughput?',
            'starter_code' => "#include <vector>\nusing namespace std;\n\nclass Solution {\npublic:\n    void transpose(vector<vector<float>>& matrix) {\n        // TODO: blocked transpose\n    }\n};",
            'hints' => "Tile size should match L1 data cache capacity.\nLoop order should favor contiguous sequential row writes.",
            'examples' => [
                [
                    'input' => 'matrix = [[1, 2], [3, 4]]',
                    'output' => '[[1, 3], [2, 4]]',
                    'explanation' => 'Rows and columns are swapped.',
                ],
            ],
            'test_cases' => [
                [
                    'name' => 'Test 1: Small matrix (4x4)',
                    'input' => 'N = 4',
                    'expected' => 'Transposed 4x4 matrix',
                    'grade' => 20,
                    'hidden' => false,
                ],
                [
                    'name' => 'Test 2: L1 Cache aligned (64x64)',
                    'input' => 'N = 64',
                    'expected' => 'Transposed 64x64 matrix',
                    'grade' => 30,
                    'hidden' => false,
                ],
                [
                    'name' => 'Test 3: Stress test (4096x4096)',
                    'input' => 'N = 4096',
                    'expected' => 'Transposed 4096x4096 matrix',
                    'grade' => 50,
                    'hidden' => true,
                ],
            ],
            'editorial_insight' => 'Block iterations into BxB tiles where B^2 * sizeof(float) fits in L1D cache.',
            'editorial_time' => 'O(N^2)',
            'editorial_space' => 'O(1)',
        ];

        $response = $this->actingAs($user)->post(route('problems.store'), $payload);

        $response->assertRedirect(route('problems.show', 'cache-aware-transpose'));

        $this->assertDatabaseHas('problems', [
            'id' => 'cache-aware-transpose',
            'title' => 'Cache-Aware Transpose',
            'difficulty' => 'Hard',
            'discipline' => 'Performance',
        ]);

        $created = Problem::find('cache-aware-transpose');
        $this->assertNotNull($created);
        $this->assertCount(3, $created->testing['sample_tests']);
        $this->assertEquals(20, $created->testing['sample_tests'][0]['grade']);
        $this->assertEquals(30, $created->testing['sample_tests'][1]['grade']);
        $this->assertEquals(50, $created->testing['sample_tests'][2]['grade']);
        $this->assertFalse($created->testing['sample_tests'][0]['hidden']);
        $this->assertTrue($created->testing['sample_tests'][2]['hidden']);
    }

    public function test_can_create_problem_via_api(): void
    {
        $user = User::factory()->create();

        $payload = [
            'title' => 'Lock-Free SPSC FIFO',
            'id' => 'lock-free-spsc-fifo',
            'difficulty' => 'Medium',
            'discipline' => 'Concurrency',
            'summary' => 'Single-producer single-consumer bounded FIFO.',
            'description' => 'Build a lock-free FIFO queue.',
            'starter_code' => '#include <atomic>\ntemplate <typename T> class Queue {};',
            'examples' => [
                [
                    'input' => 'push(1), pop()',
                    'output' => '1',
                ],
            ],
            'test_cases' => [
                [
                    'name' => 'Basic FIFO',
                    'input' => '10 items',
                    'expected' => '10 items in order',
                    'grade' => 50,
                ],
                [
                    'name' => 'Full capacity check',
                    'input' => 'overflow attempt',
                    'expected' => 'returns false',
                    'grade' => 50,
                ],
            ],
        ];

        $response = $this->actingAs($user)->postJson(route('api.problems.store'), $payload);

        $response->assertCreated()
            ->assertJsonPath('data.id', 'lock-free-spsc-fifo')
            ->assertJsonPath('data.title', 'Lock-Free SPSC FIFO');

        $this->assertDatabaseHas('problems', [
            'id' => 'lock-free-spsc-fifo',
        ]);
    }

    public function test_bundle_endpoint_returns_spec_2_bundle(): void
    {
        $user = User::factory()->create();

        // 1. Create a problem following Specification 2.0
        $payload = [
            'title' => 'FizzBuzz Enterprise',
            'id' => 'fizzbuzz-ent',
            'difficulty' => 'Easy',
            'discipline' => 'Systems',
            'topic' => 'Algorithms',
            'summary' => 'Implement FizzBuzz using C++ class.',
            'description' => 'Generate FizzBuzz sequence from 1 to n.',
            'starter_code' => "class FizzBuzz { public: std::vector<std::string> generate(int n); };",
            'judge_solution' => "namespace judge { class FizzBuzz { public: std::vector<std::string> generate(int n) { return {}; } }; }",
            'judge_test_h' => '#define ASSERT_EQ(a,b,m) if((a)!=(b)) exit(1);',
            'examples' => [
                ['input' => 'n = 3', 'output' => '["1","2","Fizz"]'],
            ],
            'test_cases' => [
                [
                    'case_id' => 'p1_one',
                    'name' => 'Single Element (n=1)',
                    'group' => 'basic',
                    'grade' => 30,
                    'hidden' => false,
                    'test_code_cpp' => '#include "judge_test.h"\nint main() { return 0; }',
                    'input' => 'n = 1',
                    'expected' => '["1"]',
                ],
                [
                    'case_id' => 'h1_zero',
                    'name' => 'Zero Input (n=0)',
                    'group' => 'edge',
                    'grade' => 70,
                    'hidden' => true,
                    'test_code_cpp' => '#include "judge_test.h"\nint main() { return 0; }',
                    'input' => 'n = 0',
                    'expected' => '[]',
                ],
            ],
        ];

        $this->actingAs($user)->post(route('problems.store'), $payload)->assertRedirect();

        // 2. Fetch bundle via GET /api/problems/{id}/bundle
        $response = $this->getJson(route('api.problems.bundle', 'fizzbuzz-ent'));

        $response->assertOk()
            ->assertJsonPath('schema_version', '2.0')
            ->assertJsonPath('problem_id', 'fizzbuzz-ent')
            ->assertJsonPath('total_points', 100)
            ->assertJsonCount(2, 'cases')
            ->assertJsonPath('cases.0.id', 'p1_one')
            ->assertJsonPath('cases.0.points', 30)
            ->assertJsonPath('cases.0.hidden', false)
            ->assertJsonPath('cases.1.id', 'h1_zero')
            ->assertJsonPath('cases.1.points', 70)
            ->assertJsonPath('cases.1.hidden', true)
            ->assertJsonStructure(['manifest_sha256']);

        $files = $response->json('files');
        $this->assertEquals($payload['judge_solution'], $files['tests/judge_solution.hpp']);
        $this->assertEquals($payload['judge_test_h'], $files['tests/judge_test.h']);
    }
}
