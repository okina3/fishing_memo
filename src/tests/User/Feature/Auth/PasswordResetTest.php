<?php

namespace Tests\User\Feature\Auth;

use App\Models\User;
use App\Notifications\User\ResetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\User\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    // パスワードリセットリンク画面が正常に表示されることをテスト。
    public function test_reset_password_link_screen_can_be_rendered(): void
    {
        $response = $this->get('/forgot-password');

        $response->assertStatus(200);
    }

    // パスワードリセットリンクが正常に送信されることをテスト。
    public function test_reset_password_link_can_be_requested(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPasswordNotification::class);
    }

    // パスワードリセット画面（通知内トークン付きURL）に正常にアクセスできることをテスト。
    public function test_reset_password_screen_can_be_rendered(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) {
            $response = $this->get('/reset-password/' . $notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    // 有効なトークンを使用してパスワードが正常にリセットできることをテスト。
    public function test_password_can_be_reset_with_valid_token(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, ResetPasswordNotification::class, function ($notification) use ($user) {
            $response = $this->post('/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'password',
                'password_confirmation' => 'password',
            ]);

            $response
                ->assertSessionHasNoErrors()
                ->assertRedirect(route('login'));

            return true;
        });
    }
}
