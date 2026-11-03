<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class ProblemsetTest extends TestCase
{
    use RefreshDatabase;

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
}
