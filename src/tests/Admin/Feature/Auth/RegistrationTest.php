<?php

namespace Tests\Admin\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Admin\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    // 登録画面が正常に表示されることをテスト。
    public function test_registration_screen_can_be_rendered(): void
    {
        $response = $this->get('/admin/register');

        $response->assertStatus(200);
    }

    // 新しい管理者が登録できることをテスト。
    public function test_new_users_can_register(): void
    {
        $response = $this->post('/admin/register', [
            'name' => 'Test Admin',
            'email' => 'test@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertAuthenticated('admin');
        $response->assertRedirect(route('admin.dashboard', absolute: false));
    }
}
