<?php

namespace Tests\Admin\Feature\Auth;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Admin\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    // ログイン画面が正常に表示されるかテスト。
    public function test_login_screen_can_be_rendered(): void
    {
        $response = $this->get('/admin/login');

        $response->assertStatus(200);
    }

    // 管理者がログイン画面を使用して認証できることを確認。
    public function test_users_can_authenticate_using_the_login_screen(): void
    {
        $user = Admin::factory()->create();

        $response = $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'password',
        ]);

        $this->assertAuthenticated('admin');
        $response->assertRedirect(route('admin.index', absolute: false));
    }

    // 無効なパスワードで管理者が認証できないことをテスト。
    public function test_users_can_not_authenticate_with_invalid_password(): void
    {
        $user = Admin::factory()->create();

        $this->post('/admin/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest('admin');
    }

    // 管理者がログアウトできることをテスト。
    public function test_users_can_logout(): void
    {
        $user = Admin::factory()->create();

        $response = $this->actingAs($user, 'admin')->post('/admin/logout');

        $this->assertGuest('admin');
        $response->assertRedirect('/admin/login');
    }
}
