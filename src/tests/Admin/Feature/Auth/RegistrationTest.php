<?php

namespace Tests\Admin\Feature\Auth;

use App\Models\Admin;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\Admin\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;
    use MockeryPHPUnitIntegration;

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

    // 既に管理者が存在する場合、登録画面はログインへリダイレクトされることをテスト。
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_registration_screen_redirects_when_admin_already_exists(): void
    {
        // 既に管理者が存在する状況をモックで作成
        Mockery::mock('alias:' . Admin::class)
            ->shouldReceive('count')
            ->andReturn(1);

        // 登録ページへアクセス
        $response = $this->get('/admin/register');

        // リダイレクト先が `route('admin.login')` であることを検証
        $response->assertRedirect(route('admin.login'));
        // セッションに管理者既存を示すステータスメッセージがセットされていることを検証
        $response->assertSessionHas('status', '管理者は既に登録されています。');
    }

    // 既に管理者が存在する場合、登録処理はログインへリダイレクトされることをテスト。
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_registration_store_redirects_when_admin_already_exists(): void
    {
        // 既に管理者が存在する状況をモックで作成
        Mockery::mock('alias:' . Admin::class)
            ->shouldReceive('count')
            ->andReturn(1);

        // 登録処理を実行
        $response = $this->post('/admin/register', [
            'name' => 'Another Admin',
            'email' => 'another@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // リダイレクト先が `route('admin.login')` であることを検証
        $response->assertRedirect(route('admin.login'));
        // セッションに管理者既存を示すステータスメッセージがセットされていることを検証
        $response->assertSessionHas('status', '管理者は既に登録されています。');
        // 認証済みの管理者がいないことを検証
        $this->assertGuest('admin');
    }

    // 登録処理中に例外が発生した場合、エラーメッセージ付きでログインへリダイレクトされることをテスト。
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function test_registration_store_failure_redirects_with_error(): void
    {
        // 登録処理中に管理者が既に存在する状況をモックで作成
        // 事前チェックは未存在（0）、再チェックで存在（1）として失敗パスを再現
        Mockery::mock('alias:' . Admin::class)
            ->shouldReceive('count')
            ->andReturn(0, 1);

        // 登録処理を実行
        $response = $this->post('/admin/register', [
            'name' => 'Fail Admin',
            'email' => 'fail@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);

        // リダイレクト先が `route('admin.login')` であることを検証
        $response->assertRedirect(route('admin.login'));
        // セッションに管理者既存を示すステータスメッセージがセットされていることを検証
        $response->assertSessionHas('status', '管理者登録に失敗しました。');
        // 認証済みの管理者がいないことを検証
        $this->assertGuest('admin');
    }
}
