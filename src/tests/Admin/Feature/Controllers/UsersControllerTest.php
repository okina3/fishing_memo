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

class UsersControllerTest extends TestCase
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

    // 全ユーザー、また、検索したユーザーを表示できることをテスト
    public function testIndexUsersController()
    {
        // ユーザーを3件作成
        User::factory()->count(3)->create();

        // 検索キーワードなしで、リクエスト送信
        $response = $this->get(route('admin.index'));

        // ステータスコード200（OK）であることを検証
        $response->assertOk();
        // 返却されるビューが期待通り（admin.users.index）であることを検証
        $response->assertViewIs('admin.users.index');
        // ビューに渡される主要なデータ（全ユーザー、検索ユーザー）が存在することを検証
        $response->assertViewHas('all_users');
    }

    // ユーザーが、正しく削除（ソフトデリート）されることをテスト
    public function testDestroyUsersController()
    {
        // 対象ユーザーを1件作成
        $user = User::factory()->create();

        // ユーザーを削除する為に、リクエスト送信
        $response = $this->delete(route('admin.destroy'), ['userId' => $user->id]);

        // リダイレクトで成功メッセージがフラッシュされていることを検証
        $response->assertRedirect(route('admin.index'));
        $response->assertSessionHas(['message' => 'ユーザーのサービス利用を停止しました。', 'status' => 'success']);

        // 対象ユーザーがソフトデリートされていることを検証
        $this->assertSoftDeleted('users', ['id' => $user->id]);
    }

    // ユーザーが、正しく削除（ソフトデリート）される時のエラーハンドリングをテスト
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function testErrorDestroyUsersController()
    {
        // 対象ユーザーを作成
        $user = User::factory()->create();

        // UserService::deleteUserShareSettingAll が例外を投げるようにエイリアスモック
        $userServiceMock = Mockery::mock('alias:App\\Services\\UserService');
        $userServiceMock->shouldReceive('deleteUserShareSettingAll')
            ->once()->andThrow(new Exception('DBエラー'));

        // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
        Log::shouldReceive('error')->once()->withAnyArgs();

        // ユーザーを削除する為に、リクエスト送信
        $response = $this->from(route('admin.index'))
            ->delete(route('admin.destroy'), ['userId' => $user->id]);

        // リダイレクトでエラーがフラッシュされていることを検証
        $response->assertRedirect(route('admin.index'));
        $response->assertSessionHas(['message' => 'ユーザーのサービス利用停止に失敗しました。', 'status' => 'error']);

        // レコードが削除されていないことを検証
        $this->assertDatabaseHas('users', [
            'id' => $user->id,
            'deleted_at' => null,
        ]);
    }
}
