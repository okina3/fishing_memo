<?php

namespace Tests\User\Feature\Controllers;

use App\Models\Memo;
use App\Models\Rod;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\User\TestCase;

class RodControllerTest extends TestCase
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

   // 釣り竿を保存するテスト
   public function testStoreRodController()
   {
      // リクエストデータを作成
      $payload = ['rod_name' => 'マスター追加竿'];

      // 釣り竿を保存するの為に、リクエスト送信
      $response = $this->post(route('user.rod.store'), $payload);

      // リダイレクトで成功メッセージがフラッシュされていることを検証
      $response->assertRedirect(route('user.masters.index', ['tab' => 'rods']));
      $response->assertSessionHas(['message' => '釣り竿を追加しました。', 'status' => 'success']);

      // 釣り竿が作成されていることを検証
      $this->assertDatabaseHas('rods', [
         'user_id' => $this->user->id,
         'name' => 'マスター追加竿',
      ]);
   }

   // 釣り竿を保存する時のエラーハンドリングをテスト
   #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
   #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
   public function testErrorStoreRodController()
   {
      // リクエストデータを作成
      $payload = ['rod_name' => '失敗竿'];

      // RodService::createRod が例外を投げるようにエイリアスモック（checkUserRod は通過）
      $serviceMock = Mockery::mock('alias:App\\Services\\User\\RodService');
      $serviceMock->shouldReceive('checkUserRod')->andReturnNull();
      $serviceMock->shouldReceive('createRod')
         ->once()->andThrow(new Exception('DBエラー'));

      // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
      Log::shouldReceive('error')->once()->withAnyArgs();

      // 釣り竿を保存するの為に、リクエスト送信
      $response = $this->from(route('user.masters.index', ['tab' => 'rods']))
         ->post(route('user.rod.store'), $payload);

      // リダイレクトでエラーメッセージがフラッシュされていることを検証
      $response->assertRedirect(route('user.masters.index', ['tab' => 'rods']));
      $response->assertSessionHas(['message' => '釣り竿の追加に失敗しました', 'status' => 'error']);

      // レコードが保存されていないことを検証
      $this->assertDatabaseMissing('rods', [
         'user_id' => $this->user->id,
         'name' => '失敗竿',
      ]);
   }

   // 釣り竿を保存するテスト（AJAX想定のJSONレスポンス）
   public function testStoreAjaxRodController()
   {
      // リクエストデータを作成
      $payload = [
         'rod_name' => 'テスト竿',
      ];

      // 釣り竿を保存するの為に、リクエスト送信
      $response = $this->post(route('user.rod.store.ajax'), $payload);

      // ステータスコード201 が返ることを検証
      $response->assertStatus(201);
      // JSONに、id とname キーが含まれる構造であることを検証
      $response->assertJsonStructure(['id', 'name']);
      // name が送信した釣り竿名と一致することを検証
      $response->assertJson(['name' => 'テスト竿']);

      // 釣り竿が作成されていることを検証
      $this->assertDatabaseHas('rods', [
         'user_id' => $this->user->id,
         'name' => 'テスト竿',
      ]);
   }

   // 釣り竿を保存する時のエラーハンドリングをテスト（AJAX想定のJSONレスポンス）
   #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
   #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
   public function testErrorStoreAjaxRodController()
   {
      // リクエストデータを作成
      $payload = [
         'rod_name' => '失敗竿',
      ];

      // RodService::createRod が例外を投げるようにエイリアスモック（checkUserRod は通過）
      $rodServiceMock = Mockery::mock('alias:App\\Services\\User\\RodService');
      $rodServiceMock->shouldReceive('checkUserRod')->andReturnNull();
      $rodServiceMock->shouldReceive('createRod')
         ->once()->andThrow(new Exception('DBエラー'));

      // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
      Log::shouldReceive('error')->once()->withAnyArgs();

      // 釣り竿を保存するの為に、リクエスト送信
      $response = $this->post(route('user.rod.store.ajax'), $payload);

      // ステータスコード500 が返ることを検証
      $response->assertStatus(500);
      // エラーがJSONで返却されていることを検証
      $response->assertJson([
         'message' => '釣り竿の登録に失敗しました。',
         'status' => 'error',
      ]);

      // レコードが保存されていないことを検証
      $this->assertDatabaseMissing('rods', [
         'user_id' => $this->user->id,
         'name' => '失敗竿',
      ]);
   }

   // 釣り竿の編集画面が正しく表示されることをテスト
   public function testEditRodController()
   {
      // 自分の釣り竿を1件作成
      $rod = Rod::factory()->create(['user_id' => $this->user->id, 'name' => '編集対象竿']);

      // 釣り竿編集画面を表示する為に、リクエスト送信
      $response = $this->get(route('user.rod.edit', ['rod' => $rod->id]));

      // ステータスコード200（OK）であることを検証
      $response->assertOk();
      // 返却されるビューが期待通りであることを検証
      $response->assertViewIs('user.masters.partials.rods.edit-rod');
      // ビューに渡される主要なデータ（選択釣り竿）が存在することを検証
      $response->assertViewHas('rod');
   }

   // 釣り竿名が正しく更新されることをテスト
   public function testUpdateRodController()
   {
      // 自分の釣り竿を1件作成
      $rod = Rod::factory()->create(['user_id' => $this->user->id, 'name' => '旧竿名']);

      // 更新用のリクエストデータを作成
      $payload = [
         'rodId' => $rod->id,
         'rod_name' => '新しい竿名',
      ];

      // 釣り竿名を更新する為に、リクエスト送信
      $response = $this->patch(route('user.rod.update'), $payload);

      // リダイレクトで成功メッセージがフラッシュされていることを検証
      $response->assertRedirect(route('user.masters.index', ['tab' => 'rods']));
      $response->assertSessionHas(['message' => '釣り竿名を更新しました。', 'status' => 'success']);

      // 釣り竿名が更新されていることを検証
      $this->assertDatabaseHas('rods', [
         'id' => $rod->id,
         'name' => '新しい竿名',
      ]);
   }

   // 釣り竿名更新時のエラーハンドリングをテスト
   #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
   #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
   public function testErrorUpdateRodController()
   {
      // 自分の釣り竿を1件作成
      $rod = Rod::factory()->create(['user_id' => $this->user->id, 'name' => '初期竿名']);

      // 更新用のリクエストデータを作成
      $payload = [
         'rodId' => $rod->id,
         'rod_name' => '更新失敗竿名',
      ];

      // RodService::updateRod が例外を投げるようにエイリアスモック（checkUserRod は通過）
      $rodServiceMock = Mockery::mock('alias:App\\Services\\User\\RodService');
      $rodServiceMock->shouldReceive('checkUserRod')->andReturnNull();
      $rodServiceMock->shouldReceive('updateRod')
         ->once()->andThrow(new Exception('DBエラー'));

      // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
      Log::shouldReceive('error')->once()->withAnyArgs();

      // 釣り竿名を更新する為に、リクエスト送信
      $response = $this->from(route('user.rod.edit', ['rod' => $rod->id]))
         ->patch(route('user.rod.update'), $payload);

      // リダイレクトでエラーがフラッシュされていることを検証
      $response->assertRedirect(route('user.rod.edit', ['rod' => $rod->id]));
      $response->assertSessionHas(['message' => '釣り竿名の更新に失敗しました。', 'status' => 'error']);

      // レコードが更新されていないことを検証
      $this->assertDatabaseMissing('rods', [
         'id' => $rod->id,
         'name' => '更新失敗竿名',
      ]);
   }

   // 釣り竿が正しく削除されることをテスト
   public function testDestroyRodController()
   {
      // 自分の釣り竿を1件作成
      $rod = Rod::factory()->create(['user_id' => $this->user->id]);

      // 釣り竿を削除する為に、リクエスト送信
      $response = $this->delete(route('user.rod.destroy'), ['rodId' => $rod->id]);

      // リダイレクトで成功メッセージがフラッシュされていることを検証
      $response->assertRedirect();
      $response->assertSessionHas(['message' => '正常に釣り竿を削除しました。', 'status' => 'success']);

      // 釣り竿が削除されていることを検証
      $this->assertDatabaseMissing('rods', ['id' => $rod->id]);
   }

   // 関連メモがある場合は削除できないことをテスト
   public function testErrorDestroyRodControllerRelatedMemo()
   {
      // 自分の釣り竿を1件作成
      $rod = Rod::factory()->create(['user_id' => $this->user->id]);

      // 釣り竿に関連するメモを1件作成し紐付ける
      $memo = Memo::factory()->create(['user_id' => $this->user->id]);
      $memo->rods()->attach($rod->id);

      // 釣り竿を削除する為に、リクエスト送信
      $response = $this->delete(route('user.rod.destroy'), ['rodId' => $rod->id]);

      // リダイレクトでエラーメッセージがフラッシュされていることを検証
      $response->assertRedirect();
      $response->assertSessionHas(['message' => '関連データのため削除できません。', 'status' => 'error']);

      // レコードが削除されていないことを確認
      $this->assertDatabaseHas('rods', ['id' => $rod->id]);
   }

   // 釣り竿削除時のエラーハンドリングをテスト
   #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
   #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
   public function testErrorDestroyRodControllerException()
   {
      // RodモデルのavailableSelectRodメソッドが例外を投げるようにエイリアスモック
      $rodAliasMock = Mockery::mock('alias:App\\Models\\Rod');
      $rodAliasMock->shouldReceive('availableSelectRod')->andThrow(new Exception('DBエラー'));

      // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
      Log::shouldReceive('error')->once()->withAnyArgs();

      // 実データを作らず、取得時に例外が発生する想定で、釣り竿削除のリクエストを送信
      $response = $this->delete(route('user.rod.destroy'), ['rodId' => 9999]);

      // リダイレクトでエラーメッセージがフラッシュされていることを検証
      $response->assertRedirect();
      $response->assertSessionHas(['message' => '釣り竿の削除に失敗しました。', 'status' => 'error']);
   }
}
