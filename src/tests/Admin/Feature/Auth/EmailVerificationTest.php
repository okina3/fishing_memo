<?php

namespace Tests\Admin\Feature\Auth;

use App\Models\Admin;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\URL;
use Tests\Admin\TestCase;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;

    // メール確認画面が正常に表示されることをテスト。
    public function test_email_verification_screen_can_be_rendered(): void
    {
        $user = Admin::factory()->unverified()->create();

        $response = $this->actingAs($user, 'admin')->get('/admin/verify-email');

        $response->assertStatus(200);
    }

    // 管理者のメールアドレスが確認できることをテスト。
    public function test_email_can_be_verified(): void
    {
        $user = Admin::factory()->unverified()->create();

        Event::fake();

        $verificationUrl = URL::temporarySignedRoute(
            'admin.verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1($user->email)]
        );

        $response = $this->actingAs($user, 'admin')->get($verificationUrl);

        Event::assertDispatched(Verified::class);
        $this->assertTrue($user->fresh()->hasVerifiedEmail());
        $response->assertRedirect(route('admin.dashboard', absolute: false) . '?verified=1');
    }

    // 無効なハッシュでメールアドレスが確認できないことをテスト。
    public function test_email_is_not_verified_with_invalid_hash(): void
    {
        $user = Admin::factory()->unverified()->create();

        $verificationUrl = URL::temporarySignedRoute(
            'admin.verification.verify',
            now()->addMinutes(60),
            ['id' => $user->id, 'hash' => sha1('wrong-email')]
        );

        $this->actingAs($user, 'admin')->get($verificationUrl);

        $this->assertFalse($user->fresh()->hasVerifiedEmail());
    }
}
