<?php

namespace Tests\Admin\Feature\Auth;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Admin\TestCase;

class PasswordConfirmationTest extends TestCase
{
    use RefreshDatabase;

    // パスワード確認画面が正常に表示されることをテスト。
    public function test_confirm_password_screen_can_be_rendered(): void
    {
        $user = Admin::factory()->create();

        $response = $this->actingAs($user, 'admin')->get('/admin/confirm-password');

        $response->assertStatus(200);
    }

    // パスワードが正常に確認できることをテスト。
    public function test_password_can_be_confirmed(): void
    {
        $user = Admin::factory()->create();

        $response = $this->actingAs($user, 'admin')->post('/admin/confirm-password', [
            'password' => 'password',
        ]);

        $response->assertRedirect();
        $response->assertSessionHasNoErrors();
    }

    // 無効なパスワードでパスワード確認が失敗することをテスト。
    public function test_password_is_not_confirmed_with_invalid_password(): void
    {
        $user = Admin::factory()->create();

        $response = $this->actingAs($user, 'admin')->post('/admin/confirm-password', [
            'password' => 'wrong-password',
        ]);

        $response->assertSessionHasErrors();
    }
}
