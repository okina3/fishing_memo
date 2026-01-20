<?php

namespace Tests\Admin\Feature\Auth;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\Admin\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    // パスワードが正常に更新できることをテスト。
    public function test_password_can_be_updated(): void
    {
        $user = Admin::factory()->create();

        $response = $this
            ->actingAs($user, 'admin')
            ->from('/admin')
            ->put('/admin/password', [
                'current_password' => 'password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/admin');

        $this->assertTrue(Hash::check('new-password', $user->refresh()->password));
    }

    // 現在の正しいパスワードでない場合、パスワード更新が失敗することをテスト。
    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = Admin::factory()->create();

        $response = $this
            ->actingAs($user, 'admin')
            ->from('/admin')
            ->put('/admin/password', [
                'current_password' => 'wrong-password',
                'password' => 'new-password',
                'password_confirmation' => 'new-password',
            ]);

        $response
            ->assertSessionHasErrorsIn('updatePassword', 'current_password')
            ->assertRedirect('/admin');
    }
}
