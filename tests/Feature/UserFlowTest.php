<?php

namespace Tests\Feature;

use App\Models\Problem;
use App\Models\Submission;
use App\Models\User;
use Database\Seeders\LearningSeeder;
use Database\Seeders\ProblemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia;
use Tests\TestCase;

class UserFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProblemSeeder::class);
        $this->seed(LearningSeeder::class);
    }

    public function test_welcome_page_renders_successfully(): void
    {
        $response = $this->get(route('home'));

        $response->assertOk();
        $response->assertInertia(fn (AssertableInertia $page) => $page
            ->component('welcome')
        );
    }

    public function test_guest_is_redirected_to_login_and_then_to_intended_problem(): void
    {
        // 1. Guest attempts to access a problem directly
        $guestResponse = $this->get(route('problems.show', 'ring-buffer'));
        $guestResponse->assertRedirect(route('login'));

        // 2. User registers or logs in
        $user = User::factory()->create();

        $loginResponse = $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ]);

        // 3. User is redirected to the intended problem
        $this->assertAuthenticatedAs($user);
        $loginResponse->assertRedirect(route('problems.show', 'ring-buffer'));

        // 4. Problem page renders for the user with problem details and navigation
        $problemResponse = $this->actingAs($user)->get(route('problems.show', 'ring-buffer'));
        $problemResponse->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('problems/show')
                ->where('problem.id', 'ring-buffer')
                ->where('isSolved', false)
                ->has('nextProblem')
                ->has('prevProblem')
                ->where('totalProblems', 18)
            );
    }

    public function test_problem_solving_workflow_with_runner_and_submission(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 1. Submit incomplete starter code with TODO -> Wrong Answer
        $todoResponse = $this->postJson(route('api.problems.submissions.store', 'ring-buffer'), [
            'language' => 'cpp',
            'code' => '// TODO: implement ring buffer',
        ]);

        $todoResponse->assertCreated()
            ->assertJsonPath('is_solved', false)
            ->assertJsonPath('submission.status', 'wrong_answer');

        // Verify problem is still unsolved for user
        $this->get(route('problems.show', 'ring-buffer'))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('isSolved', false)
            );

        // 2. Submit working solution -> Accepted
        $solveResponse = $this->postJson(route('api.problems.submissions.store', 'ring-buffer'), [
            'language' => 'cpp',
            'code' => 'template <typename T, size_t Cap> class RingBuffer { public: bool push(T val) { return true; } };',
        ]);

        $solveResponse->assertCreated()
            ->assertJsonPath('is_solved', true)
            ->assertJsonPath('submission.status', 'accepted');

        // 3. Problem show page now reflects isSolved = true
        $this->get(route('problems.show', 'ring-buffer'))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('isSolved', true)
                ->has('initialSubmissions', 2)
            );

        // 4. Problemset list page now contains ring-buffer in solvedProblemIds
        $this->get(route('problemset.index'))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('solvedProblemIds.0', 'ring-buffer')
            );

        // 5. Dashboard now has ring-buffer in solvedProblemIds
        $this->get(route('dashboard'))
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('solvedProblemIds.0', 'ring-buffer')
            );
    }

    public function test_problem_navigation_sequence(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $response = $this->get(route('problems.show', 'two-sum'));
        $response->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('problem.id', 'two-sum')
                ->where('prevProblem', null)
                ->has('nextProblem')
            );

        $nextId = $response->inertiaPage()['props']['nextProblem']['id'];

        $nextResponse = $this->get(route('problems.show', $nextId));
        $nextResponse->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->where('problem.id', $nextId)
                ->has('prevProblem')
                ->where('prevProblem.id', 'two-sum')
            );
    }

    public function test_learning_track_and_lesson_flow_to_practice_challenge(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        // 1. Visit tracks list
        $this->get(route('learn.index'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('learn/index')
                ->has('dbTracks', 3)
            );

        // 2. Visit track detail
        $this->get(route('learn.show', 'cpp-foundations'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('learn/show')
                ->where('track', 'cpp-foundations')
            );

        // 3. Visit lesson
        $this->get(route('learn.lesson', ['track' => 'cpp-foundations', 'lesson' => 'references-and-views']))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page
                ->component('learn/lesson')
                ->where('lesson', 'references-and-views')
            );
    }

    public function test_all_auth_views_render_properly(): void
    {
        // 1. Login view
        $this->get(route('login'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component('auth/login'));

        // 2. Register view
        $this->get(route('register'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component('auth/register'));

        // 3. Forgot password view
        $this->get(route('password.request'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component('auth/forgot-password'));

        // 4. Email verification prompt view (if enabled)
        if (\Laravel\Fortify\Features::enabled(\Laravel\Fortify\Features::emailVerification())) {
            $unverifiedUser = User::factory()->unverified()->create();
            $this->actingAs($unverifiedUser)
                ->get(route('verification.notice'))
                ->assertOk()
                ->assertInertia(fn (AssertableInertia $page) => $page->component('auth/verify-email'));
        }

        // 5. Password confirmation view
        $verifiedUser = User::factory()->create();
        $this->actingAs($verifiedUser)
            ->get(route('password.confirm'))
            ->assertOk()
            ->assertInertia(fn (AssertableInertia $page) => $page->component('auth/confirm-password'));
    }
}
