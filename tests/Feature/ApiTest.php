<?php

namespace Tests\Feature;

use App\Models\DailyChallenge;
use App\Models\Lesson;
use App\Models\Problem;
use App\Models\Track;
use App\Models\User;
use Database\Seeders\LearningSeeder;
use Database\Seeders\ProblemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProblemSeeder::class);
        $this->seed(LearningSeeder::class);
    }

    public function test_can_list_problems_via_api(): void
    {
        $response = $this->getJson(route('api.problems.index'));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'number', 'title', 'difficulty', 'acceptance_rate', 'tags', 'summary'],
                ],
                'total',
            ]);

        $this->assertGreaterThanOrEqual(18, $response->json('total'));
    }

    public function test_can_show_problem_via_api(): void
    {
        $response = $this->getJson(route('api.problems.show', 'two-sum'));

        $response->assertOk()
            ->assertJsonPath('data.id', 'two-sum')
            ->assertJsonPath('data.title', 'Two Sum')
            ->assertJsonStructure([
                'data' => [
                    'id', 'title', 'difficulty', 'description', 'examples', 'constraints', 'starter_code', 'testing',
                ],
                'is_solved',
            ]);
    }

    public function test_can_create_submission_for_problem(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('api.problems.submissions.store', 'two-sum'), [
            'language' => 'cpp',
            'code' => '#include <vector>\nusing namespace std;\nclass Solution { public: vector<int> twoSum(vector<int>& n, int t) { return {0, 1}; } };',
        ]);

        $response->assertCreated()
            ->assertJsonPath('is_solved', true)
            ->assertJsonStructure([
                'message',
                'submission' => ['id', 'user_id', 'problem_id', 'language', 'status'],
                'is_solved',
            ]);

        $this->assertDatabaseHas('submissions', [
            'user_id' => $user->id,
            'problem_id' => 'two-sum',
            'status' => 'accepted',
        ]);
    }

    public function test_can_list_user_submissions(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->postJson(route('api.problems.submissions.store', 'ring-buffer'), [
            'language' => 'cpp',
            'code' => '// solution implementation',
        ]);

        $response = $this->actingAs($user)->getJson(route('api.problems.submissions.index', 'ring-buffer'));

        $response->assertOk()
            ->assertJsonCount(1, 'data')
            ->assertJsonPath('data.0.problem_id', 'ring-buffer');
    }

    public function test_can_list_learn_tracks_via_api(): void
    {
        $response = $this->getJson(route('api.learn.tracks'));

        $response->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['slug', 'title', 'level', 'hours', 'progress'],
                ],
            ]);
    }

    public function test_can_show_learn_track_via_api(): void
    {
        $response = $this->getJson(route('api.learn.track', 'cpp-foundations'));

        $response->assertOk()
            ->assertJsonPath('data.slug', 'cpp-foundations')
            ->assertJsonStructure([
                'data' => ['slug', 'title', 'modules'],
            ]);
    }

    public function test_can_show_lesson_via_api(): void
    {
        $response = $this->getJson(route('api.learn.lesson', ['track' => 'cpp-foundations', 'lesson' => 'value-categories']));

        $response->assertOk()
            ->assertJsonPath('data.lesson.slug', 'value-categories')
            ->assertJsonStructure([
                'data' => ['track', 'lesson' => ['slug', 'title', 'blocks', 'quiz']],
            ]);
    }

    public function test_can_toggle_lesson_completion_via_api(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('api.learn.lesson.complete', [
            'track' => 'cpp-foundations',
            'lesson' => 'value-categories',
        ]));

        $response->assertOk()
            ->assertJsonPath('completed', true);

        $this->assertDatabaseHas('user_lesson_progress', [
            'user_id' => $user->id,
            'lesson_id' => 'value-categories',
            'completed' => true,
        ]);
    }
}
