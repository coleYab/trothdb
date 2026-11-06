<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\LearningSeeder;
use Database\Seeders\ProblemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ProblemsetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProblemSeeder::class);
        $this->seed(LearningSeeder::class);
    }

    public function test_guests_are_redirected_to_the_login_page()
    {
        $this->get(route('problemset.index'))
            ->assertRedirect(route('login'));

        $this->get(route('problems.show', 'two-sum'))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_users_can_browse_the_problemset()
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('problemset.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('problems/index')
                ->has('problems', count(config('problems')))
                ->has('problems.0', fn (AssertableInertia $problem) => $problem
                    ->has('id')
                    ->has('number')
                    ->has('title')
                    ->has('difficulty')
                    ->has('acceptance_rate')
                    ->has('tags')
                    ->has('summary')
                    ->etc()
                )
            );
    }

    public function test_authenticated_users_can_view_a_problem()
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('problems.show', 'two-sum'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('problems/show')
                ->where('problem.id', 'two-sum')
                ->has('problem.description')
                ->has('problem.examples')
                ->has('problem.constraints')
                ->has('problem.starter_code')
                ->has('problem.testing.judge')
                ->has('problem.testing.sample_tests')
                ->has('problem.hints')
                ->etc()
            );
    }

    public function test_unknown_problems_return_not_found()
    {
        $this->actingAs(User::factory()->create());

        $this->get(route('problems.show', 'does-not-exist'))
            ->assertNotFound();
    }

    public function test_shows_solved_state_and_navigation_metadata()
    {
        $user = User::factory()->create();
        \App\Models\Submission::create([
            'user_id' => $user->id,
            'problem_id' => 'two-sum',
            'language' => 'cpp',
            'code' => 'vector<int> twoSum() { return {0,1}; }',
            'status' => 'accepted',
            'runtime_ms' => 4,
            'memory_mb' => 14.2,
        ]);

        $this->actingAs($user);

        // Problemset list has solvedProblemIds
        $this->get(route('problemset.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('problems/index')
                ->has('solvedProblemIds', 1)
                ->where('solvedProblemIds.0', 'two-sum')
            );

        // Problem show has isSolved true and nextProblem
        $this->get(route('problems.show', 'two-sum'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('problems/show')
                ->where('isSolved', true)
                ->where('problem.id', 'two-sum')
                ->has('nextProblem')
                ->where('nextProblem.number', 4)
            );
    }
}
