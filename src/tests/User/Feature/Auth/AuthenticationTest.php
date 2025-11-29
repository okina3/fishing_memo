<?php

namespace Tests\User\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\User\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    // ログイン画面が正常に表示されるかテスト。
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);
    }


    // ユーザーがログイン画面を使用して認証できることを確認。
    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated('users');
        $response->assertRedirect(route('user.index', absolute: false));
    }

    // 無効なパスワードでユーザーが認証できないことをテスト。
    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = User::factory()->create();

        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest('users');
    }

    // ユーザーがログアウトできることをテスト。
    public function test_users_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'users')->post('/logout');

        $this->assertGuest('users');
        $response->assertRedirect('/');
    }
}
