<?php

namespace Tests\Admin\Feature\Controllers;

use App\Models\Admin;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\Admin\TestCase;

class WarningUsersControllerTest extends TestCase
{
    use RefreshDatabase;
    use MockeryPHPUnitIntegration;

    private Admin $admin;

    // テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
    protected function setUp(): void
    {
        // 親クラスのsetUpメソッドを呼び出し
        parent::setUp();
        // 管理者を作成
        $this->admin = Admin::factory()->create();
        // 認証済みの管理者を返す
        $this->actingAs($this->admin, 'admin');
    }

    // 警告したユーザー一覧が、正しく表示されることをテスト
    public function testIndexWarningUsersController()
    {
        // 3件のソフトデリートされたユーザーを作成
        User::factory()->count(3)->create(['deleted_at' => now()]);

        // 認証済み管理者で一覧ルートへアクセス
        $response = $this->get(route('admin.warning.index'));

        // ステータスコード200（OK）であることを検証
        $response->assertOk();
        // 返却されるビューが期待通り（admin.warningUsers.index）であることを検証
        $response->assertViewIs('admin.warningUsers.index');
        // ビューに渡される主要なデータ（ソフトデリートされたユーザー）が存在することを検証
        $response->assertViewHasAll(['all_warning_users']);
    }

    // 警告したユーザーを元に戻せることをテスト
    public function testUndoWarningUsersController()
    {
        // 1件のソフトデリートされたユーザーを作成
        $user = User::factory()->create(['deleted_at' => now()]);

        // 元に戻す為に、リクエストを送信
        $response = $this->patch(route('admin.warning.undo'), ['userId' => $user->id]);

        // リダイレクトで成功メッセージがフラッシュされていることを検証
        $response->assertRedirect(route('admin.warning.index'));
        $response->assertSessionHas(['message' => 'ユーザーのサービス利用を再開しました', 'status' => 'success']);

        // ユーザーが元に戻されたことを確認
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => null,
        ]);
    }

    // 警告したユーザーを完全削除できることをテスト
    public function testDestroyWarningUsersController()
    {
        // 1件のソフトデリートされたユーザーを作成
        $user = User::factory()->create(['deleted_at' => now()]);

        // ソフトデリートされたユーザーを、完全削除する為にリクエストを送信
        $response = $this->delete(route('admin.warning.destroy'), ['userId' => $user->id]);

        // リダイレクトで成功メッセージがフラッシュされていることを検証
        $response->assertRedirect(route('admin.warning.index'));
        $response->assertSessionHas(['message' => 'ユーザーの情報を完全に削除しました。', 'status' => 'success']);

        // ユーザーが完全に削除されたことを確認
        $this->assertDatabaseMissing('users', [
            'id' => $user->id,
        ]);
    }

    // 警告したユーザーの完全削除時のエラーハンドリングをテスト
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function testErrorDestroyWarningUsersController()
    {
        // 1件のソフトデリートされたユーザーを作成
        $user = User::factory()->create(['deleted_at' => now()]);

        // WarningUsersService::permanentlyDeleteUser が例外を投げるようにエイリアスモック
        $serviceMock = Mockery::mock('alias:App\\Services\\WarningUsersService');
        $serviceMock->shouldReceive('permanentlyDeleteUser')
            ->once()->andThrow(new Exception('forced error'));

        // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
        Log::shouldReceive('error')->once()->withAnyArgs();

        // ソフトデリートされたユーザーを、完全削除する為にリクエストを送信
        $response = $this->from(route('admin.warning.index'))
            ->delete(route('admin.warning.destroy'), ['userId' => $user->id]);

        // リダイレクトでエラーがフラッシュされていることを検証
        $response->assertRedirect(route('admin.warning.index'));
        $response->assertSessionHas(['message' => 'ユーザーの完全削除に失敗しました。', 'status' => 'error']);

        // レコードが削除されていないことを検証
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
        ]);
    }
}
