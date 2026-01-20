<?php

namespace Tests\Common\Middleware;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use Tests\Common\TestCase;

class AuthenticateTest extends TestCase
{
    use RefreshDatabase;

    // 管理者の未認証アクセスは、管理者ログインへリダイレクトされることをテスト
    public function test_admin_redirects_to_login(): void
    {
        // テスト専用の管理者保護ルートを定義
        Route::middleware(['web', 'auth:admin'])
            ->get('/admin/test-protected', function () {
                return response('ok');
            })
            ->name('admin.test-protected');

        // 未認証でアクセス -> 管理者ログインへリダイレクト
        $response = $this->get('/admin/test-protected');
        $response->assertRedirect(route('admin.login'));
    }

    // ユーザーの未認証アクセスは、ユーザーログインへリダイレクトされることをテスト
    public function test_user_redirects_to_login(): void
    {
        // テスト専用のユーザー保護ルートを定義
        Route::middleware(['web', 'auth'])
            ->get('/test-protected', function () {
                return response('ok');
            })
            ->name('user.test-protected');

        // 未認証でアクセス -> ユーザーログインへリダイレクト
        $response = $this->get('/test-protected');
        $response->assertRedirect(route('login'));
    }

    // JSONを期待する未認証アクセスでは、リダイレクトせず 401 を返すことをテスト
    public function test_json_unauth_returns_401(): void
    {
        // テスト専用のユーザー保護API風ルートを定義
        Route::middleware(['web', 'auth'])
            ->get('/test-protected-json', function () {
                return response('ok');
            })
            ->name('user.test-protected-json');

        // JSONを期待するヘッダを付与して未認証アクセス
        $response = $this->getJson('/test-protected-json');

        // リダイレクトではなく 401 を返す
        $response->assertStatus(401);
        $response->assertHeaderMissing('Location');
    }
}
