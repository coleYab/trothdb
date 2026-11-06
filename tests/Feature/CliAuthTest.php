<?php

namespace Tests\Feature;

use App\Models\DeviceAuthorization;
use App\Models\User;
use Database\Seeders\ProblemSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CliAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(ProblemSeeder::class);
    }

    public function test_judge_can_request_authorization(): void
    {
        $response = $this->postJson(route('api.judge.auth.request'), [
            'client_name' => 'LocalJudge v1.0',
            'callback_port' => 54321,
        ]);

        $response->assertOk()
            ->assertJsonPath('status', 'pending')
            ->assertJsonStructure([
                'status',
                'device_code',
                'user_code',
                'verification_url',
                'expires_in',
                'interval',
            ]);

        $this->assertDatabaseHas('device_authorizations', [
            'client_name' => 'LocalJudge v1.0',
            'callback_port' => 54321,
            'status' => 'pending',
        ]);
    }

    public function test_judge_polling_returns_pending_until_approved(): void
    {
        $req = DeviceAuthorization::createRequest('LocalJudge');

        $response = $this->postJson(route('api.judge.auth.poll'), [
            'device_code' => $req->device_code,
        ]);

        $response->assertOk()
            ->assertJsonPath('status', 'pending');
    }

    public function test_guest_is_redirected_to_login_when_opening_authorization_page(): void
    {
        $req = DeviceAuthorization::createRequest('LocalJudge');

        $this->get(route('cli.authorize', ['code' => $req->user_code]))
            ->assertRedirect(route('login'));
    }

    public function test_authenticated_user_can_view_authorization_page(): void
    {
        $user = User::factory()->create();
        $req = DeviceAuthorization::createRequest('LocalJudge');

        $this->actingAs($user)
            ->get(route('cli.authorize', ['code' => $req->user_code]))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('cli/authorize')
                ->where('authRequest.user_code', $req->user_code)
                ->where('authRequest.client_name', 'LocalJudge')
            );
    }

    public function test_user_approves_and_judge_receives_token(): void
    {
        $user = User::factory()->create(['username' => 'testjudge']);
        $req = DeviceAuthorization::createRequest('LocalJudge', 54321);

        // 1. User approves via web
        $approveResponse = $this->actingAs($user)->postJson(route('cli.authorize.approve'), [
            'device_code' => $req->device_code,
        ]);

        $approveResponse->assertOk()
            ->assertJsonPath('status', 'approved')
            ->assertJsonStructure(['token', 'callback_url']);

        $token = $approveResponse->json('token');
        $this->assertStringStartsWith('arena_live_', $token);

        // 2. LocalJudge polls and receives token
        $pollResponse = $this->postJson(route('api.judge.auth.poll'), [
            'device_code' => $req->device_code,
        ]);

        $pollResponse->assertOk()
            ->assertJsonPath('status', 'approved')
            ->assertJsonPath('token', $token)
            ->assertJsonPath('user.username', 'testjudge');

        // 3. LocalJudge uses token to authenticate
        $whoamiResponse = $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson(route('api.judge.whoami'));

        $whoamiResponse->assertOk()
            ->assertJsonPath('authenticated', true)
            ->assertJsonPath('user.username', 'testjudge');
    }

    public function test_user_can_deny_authorization(): void
    {
        $user = User::factory()->create();
        $req = DeviceAuthorization::createRequest('LocalJudge');

        $this->actingAs($user)->postJson(route('cli.authorize.deny'), [
            'device_code' => $req->device_code,
        ]);

        $this->assertDatabaseHas('device_authorizations', [
            'id' => $req->id,
            'status' => 'denied',
        ]);

        $pollResponse = $this->postJson(route('api.judge.auth.poll'), [
            'device_code' => $req->device_code,
        ]);

        $pollResponse->assertStatus(403)
            ->assertJsonPath('status', 'denied');
    }
}
