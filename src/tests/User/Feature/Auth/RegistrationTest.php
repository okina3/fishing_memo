<?php

namespace Tests\User\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\User\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    // 登録画面が正常に表示されることをテスト。
    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    // 新しいユーザーが登録できることをテスト。
    public function test_new_users_can_register(): void
    {
        $response = $this->post('/register', [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated('users');
        $response->assertRedirect(route('user.dashboard', absolute: false));
    }
}
