<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->skipUnlessFortifyHas(Features::registration());
    }

    public function test_registration_screen_can_be_rendered()
    {
        $response = $this->get(route('register'));

        $response->assertOk();
    }

    public function test_new_users_can_register()
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_new_users_can_register_with_username()
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Test User',
            'username' => 'test_coder.99',
            'email' => 'testcoder@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated();
        $this->assertDatabaseHas('users', [
            'email' => 'testcoder@example.com',
            'username' => 'test_coder.99',
        ]);
        $response->assertRedirect(route('dashboard', absolute: false));
    }

    public function test_username_cannot_have_spaces()
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Test User',
            'username' => 'bad user name',
            'email' => 'badspace@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_username_cannot_exceed_14_characters()
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Test User',
            'username' => 'toolongusername15',
            'email' => 'toolong@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_username_only_allows_letters_digits_underscores_and_dots()
    {
        $response = $this->post(route('register.store'), [
            'name' => 'Test User',
            'username' => 'coder$bad!',
            'email' => 'badchars@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }
}
