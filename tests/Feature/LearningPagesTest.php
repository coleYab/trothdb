<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\LearningSeeder;
use Database\Seeders\ProblemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class LearningPagesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProblemSeeder::class);
        $this->seed(LearningSeeder::class);
    }

    /**
     * @return array<string, array{string}>
     */
    public static function pageProvider(): array
    {
        return [
            'learn' => ['learn.index'],
            'track detail' => ['learn.show'],
            'lesson' => ['learn.lesson'],
            'setup' => ['setup.index'],
        ];
    }

    #[DataProvider('pageProvider')]
    public function test_guests_are_redirected_to_the_login_page(string $route): void
    {
        $this->get(route($route, $this->parametersFor($route)))
            ->assertRedirect(route('login'));
    }

    #[DataProvider('pageProvider')]
    public function test_authenticated_users_can_visit_each_learning_page(string $route): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $this->get(route($route, $this->parametersFor($route)))
            ->assertOk();
    }

    /**
     * @return array<string, string>
     */
    private function parametersFor(string $route): array
    {
        return match ($route) {
            'learn.show' => ['track' => 'cpp-foundations'],
            'learn.lesson' => [
                'track' => 'cpp-foundations',
                'lesson' => 'value-categories',
            ],
            default => [],
        };
    }
}
