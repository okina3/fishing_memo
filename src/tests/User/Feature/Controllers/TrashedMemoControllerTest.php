<?php

namespace Tests\User\Feature\Controllers;

use App\Models\Memo;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\User\TestCase;

class TrashedMemoControllerTest extends TestCase
{
    use RefreshDatabase;
    use MockeryPHPUnitIntegration;

    private User $user;

    // テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
    protected function setUp(): void
    {
        // 親クラスのsetUpメソッドを呼び出し
        parent::setUp();
        // ユーザーを作成
        $this->user = User::factory()->create();
        // 認証済みのユーザーを返す
        $this->actingAs($this->user, 'users');
    }

    // ソフトデリートしたメモ一覧が、正しく表示されることをテスト
    public function testIndexTrashedMemoController()
    {
        // 認証済みユーザーでメモ一覧ルートへアクセス
        $response = $this->get(route('user.trashed-memo.index'));

        // ステータスコード200（OK）であることを検証
        $response->assertOk();
        // 返却されるビューが期待通り（user.trashedMemos.index）であることを検証
        $response->assertViewIs('user.trashedMemos.index');
        // ビューに渡される主要なデータ（ソフトデリートされたメモ）が存在することを検証
        $response->assertViewHasAll(['all_trashed_memos']);
    }

    // ソフトデリートしたメモを元に戻せることをテスト
    public function testUndoTrashedMemoController()
    {
        // 1件のソフトデリートされたメモを作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id, 'deleted_at' => now()]);

        // ソフトデリートしたメモを、元に戻す為に、リクエストを送信
        $response = $this->patch(route('user.trashed-memo.undo'), ['memoId' => $memo->id]);

        // リダイレクトで成功メッセージがフラッシュされていることを検証
        $response->assertRedirect(route('user.trashed-memo.index'));
        $response->assertSessionHas(['message' => 'メモを元に戻しました。', 'status' => 'success']);

        // メモが元に戻されたことを確認
        $this->assertDatabaseHas('memos', [
            'id' => $memo->id,
            'deleted_at' => null,
        ]);
    }

    // ソフトデリートしたメモを完全削除できることをテスト
    public function testDestroyTrashedMemoController()
    {
        // 1件のソフトデリートされたメモを作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id, 'deleted_at' => now()]);

        // ソフトデリートしたメモを、完全削除する為に、リクエストを送信
        $response = $this->delete(route('user.trashed-memo.destroy'), ['memoId' => $memo->id]);

        // リダイレクトで成功メッセージがフラッシュされていることを検証
        $response->assertRedirect(route('user.trashed-memo.index'));
        $response->assertSessionHas(['message' => 'メモを完全に削除しました。', 'status' => 'success']);

        // メモが完全に削除されたことを確認
        $this->assertDatabaseMissing('memos', [
            'id' => $memo->id
        ]);
    }

    // ソフトデリートしたメモを完全削除する際のエラーハンドリングをテスト
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function testErrorDestroyTrashedMemoController()
    {
        // テスト用にソフトデリートされたメモを作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id, 'deleted_at' => now()]);

        // TrashedMemoService::deleteRelatedRecords が例外を投げるようにエイリアスモック
        $trashedMemoServiceMock = Mockery::mock('alias:App\\Services\\TrashedMemoService');
        $trashedMemoServiceMock->shouldReceive('deleteRelatedRecords')
            ->once()->andThrow(new Exception('forced error'));

        // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
        Log::shouldReceive('error')->once()->withAnyArgs();

        // ソフトデリートしたメモの削除リクエストを送信
        $response = $this->from(route('user.trashed-memo.index'))
            ->delete(route('user.trashed-memo.destroy'), ['memoId' => $memo->id]);

        // リダイレクトでエラーがフラッシュされていることを検証
        $response->assertRedirect(route('user.trashed-memo.index'));
        $response->assertSessionHas(['message' => 'メモの完全削除に失敗しました。', 'status' => 'error']);

        // レコードが削除されていないことを検証
        $this->assertDatabaseHas('memos', [
            'id' => $memo->id,
        ]);
    }
}
