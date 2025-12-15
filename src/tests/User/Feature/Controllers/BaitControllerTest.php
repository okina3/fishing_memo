<?php

namespace Tests\User\Feature\Controllers;

use App\Models\Bait;
use App\Models\Memo;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\User\TestCase;

class BaitControllerTest extends TestCase
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

   // エサを保存するテスト
   public function testStoreBaitController()
   {
      // リクエストデータを作成
      $payload = ['bait_name' => 'マスター追加エサ'];

      // エサを保存するの為に、リクエスト送信
      $response = $this->post(route('user.bait.store'), $payload);

      // リダイレクトで成功メッセージがフラッシュされていることを検証
      $response->assertRedirect(route('user.masters.index', ['tab' => 'baits']));
      $response->assertSessionHas(['message' => 'エサを追加しました。', 'status' => 'success']);

      // エサが作成されていることを検証
      $this->assertDatabaseHas('baits', [
         'user_id' => $this->user->id,
         'name' => 'マスター追加エサ',
      ]);
   }

   // エサを保存する時のエラーハンドリングをテスト
   #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
   #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
   public function testErrorStoreBaitController()
   {
      // リクエストデータを作成
      $payload = ['bait_name' => '失敗エサ'];

      // BaitService::createBait が例外を投げるようにエイリアスモック（checkUserBait は通過）
      $serviceMock = Mockery::mock('alias:App\\Services\\BaitService');
      $serviceMock->shouldReceive('checkUserBait')->andReturnNull();
      $serviceMock->shouldReceive('createBait')
         ->once()->andThrow(new Exception('DBエラー'));

      // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
      Log::shouldReceive('error')->once()->withAnyArgs();

      // エサを保存するの為に、リクエスト送信
      $response = $this->from(route('user.masters.index', ['tab' => 'baits']))
         ->post(route('user.bait.store'), $payload);

      // リダイレクトでエラーメッセージがフラッシュされていることを検証
      $response->assertRedirect(route('user.masters.index', ['tab' => 'baits']));
      $response->assertSessionHas(['message' => 'エサの追加に失敗しました', 'status' => 'error']);

      // レコードが保存されていないことを検証
      $this->assertDatabaseMissing('baits', [
         'user_id' => $this->user->id,
         'name' => '失敗エサ',
      ]);
   }

   // エサの編集画面が正しく表示されることをテスト
   public function testEditBaitController()
   {
      // 自分のエサを1件作成
      $bait = Bait::factory()->create(['user_id' => $this->user->id, 'name' => '編集対象エサ']);

      // エサ編集画面を表示する為に、リクエストを送信
      $response = $this->get(route('user.bait.edit', ['bait' => $bait->id]));

      // ステータスコード200（OK）であることを検証
      $response->assertOk();
      // 返却されるビューが期待通り（user.masters.edit-bait）であることを検証
      $response->assertViewIs('user.masters.edit-bait');
      // ビューに渡される主要なデータ（選択エサ）が存在することを検証
      $response->assertViewHas('bait');
   }

   // エサ名が正しく更新されることをテスト
   public function testUpdateBaitController()
   {
      // 自分のエサを1件作成
      $bait = Bait::factory()->create(['user_id' => $this->user->id, 'name' => '旧エサ名']);

      // 更新用のリクエストデータを作成
      $payload = [
         'baitId' => $bait->id,
         'bait_name' => '新しいエサ名',
      ];

      // エサ名を更新する為に、リクエストを送信
      $response = $this->patch(route('user.bait.update'), $payload);

      // リダイレクトで成功メッセージがフラッシュされていることを検証
      $response->assertRedirect(route('user.masters.index', ['tab' => 'baits']));
      $response->assertSessionHas(['message' => 'エサ名を更新しました。', 'status' => 'success']);

      // エサ名が更新されていることを検証
      $this->assertDatabaseHas('baits', [
         'id' => $bait->id,
         'name' => '新しいエサ名',
      ]);
   }

   // エサ名更新時のエラーハンドリングをテスト
   #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
   #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
   public function testErrorUpdateBaitController()
   {
      // 自分のエサを1件作成
      $bait = Bait::factory()->create(['user_id' => $this->user->id, 'name' => '初期エサ名']);

      // 更新用のリクエストデータを作成
      $payload = [
         'baitId' => $bait->id,
         'bait_name' => '更新失敗エサ名',
      ];

      // BaitService::updateBait が例外を投げるようにエイリアスモック（checkUserBait は通過）
      $baitServiceMock = Mockery::mock('alias:App\\Services\\BaitService');
      $baitServiceMock->shouldReceive('checkUserBait')->andReturnNull();
      $baitServiceMock->shouldReceive('updateBait')
         ->once()->andThrow(new Exception('DBエラー'));

      // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
      Log::shouldReceive('error')->once()->withAnyArgs();

      // エサ名を更新する為に、リクエストを送信
      $response = $this->from(route('user.bait.edit', ['bait' => $bait->id]))
         ->patch(route('user.bait.update'), $payload);

      // リダイレクトでエラーがフラッシュされていることを検証
      $response->assertRedirect(route('user.bait.edit', ['bait' => $bait->id]));
      $response->assertSessionHas(['message' => 'エサ名の更新に失敗しました。', 'status' => 'error']);

      // レコードが更新されていないことを検証
      $this->assertDatabaseMissing('baits', [
         'id' => $bait->id,
         'name' => '更新失敗エサ名',
      ]);
   }

   // エサが正しく削除されることをテスト
   public function testDestroyBaitController()
   {
      // 自分のエサを1件作成
      $bait = Bait::factory()->create(['user_id' => $this->user->id]);

      // エサを削除する為に、リクエストを送信
      $response = $this->delete(route('user.bait.destroy'), ['baitId' => $bait->id]);

      // リダイレクトで成功メッセージがフラッシュされていることを検証
      $response->assertRedirect();
      $response->assertSessionHas(['message' => '正常にエサを削除しました。', 'status' => 'success']);

      // エサが削除されたことを確認
      $this->assertDatabaseMissing('baits', [
         'id' => $bait->id,
      ]);
   }

   // 関連メモがある場合は削除できないことをテスト
   public function testErrorDestroyBaitControllerRelatedMemo()
   {
      // 自分のエサを1件作成
      $bait = Bait::factory()->create(['user_id' => $this->user->id]);

      // エサに関連するメモを1件作成し紐付ける
      $memo = Memo::factory()->create(['user_id' => $this->user->id]);
      $memo->baits()->attach($bait->id);

      // エサを削除する為に、リクエストを送信
      $response = $this->delete(route('user.bait.destroy'), ['baitId' => $bait->id]);

      // リダイレクトでエラーメッセージがフラッシュされていることを検証
      $response->assertRedirect();
      $response->assertSessionHas(['message' => '関連データのため削除できません。', 'status' => 'error']);

      // エサが削除されていないことを確認
      $this->assertDatabaseHas('baits', [
         'id' => $bait->id,
      ]);
   }

   // エサ削除時の例外ハンドリングをテスト
   #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
   #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
   public function testErrorDestroyBaitControllerException()
   {
      // BaitモデルのavailableSelectBait メソッドが例外を投げるようにエイリアスモック
      $baitAlias = Mockery::mock('alias:App\\Models\\Bait');
      $baitAlias->shouldReceive('availableSelectBait')->andThrow(new Exception('DBエラー'));

      // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
      Log::shouldReceive('error')->once()->withAnyArgs();

      // 実データを作らず、取得時に例外が発生する想で、エサ削除のリクエストを送信
      $response = $this->delete(route('user.bait.destroy'), ['baitId' => 99999]);

      // リダイレクトでエラーがフラッシュされていることを検証
      $response->assertRedirect();
      $response->assertSessionHas(['message' => 'エサの削除に失敗しました。', 'status' => 'error']);
   }
}
