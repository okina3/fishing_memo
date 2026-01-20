<?php

namespace Tests\User\Feature\Controllers;

use App\Models\Hook;
use App\Models\Memo;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\User\TestCase;

class HookControllerTest extends TestCase
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

   // 釣り針を保存するテスト
   public function testStoreHookController()
   {
      // リクエストデータを作成
      $payload = ['hook_name' => 'マスター追加釣り針'];

      // 釣り針を保存するの為に、リクエスト送信
      $response = $this->post(route('user.hook.store'), $payload);

      // リダイレクトで成功メッセージがフラッシュされていることを検証
      $response->assertRedirect(route('user.masters.index', ['tab' => 'hooks']));
      $response->assertSessionHas(['message' => '釣り針を追加しました。', 'status' => 'success']);

      // 釣り針が作成されていることを検証
      $this->assertDatabaseHas('hooks', [
         'user_id' => $this->user->id,
         'name' => 'マスター追加釣り針',
      ]);
   }

   // 釣り針を保存する時のエラーハンドリングをテスト
   #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
   #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
   public function testErrorStoreHookController()
   {
      // リクエストデータを作成
      $payload = ['hook_name' => '失敗釣り針'];

      // HookService::createHook が例外を投げるようにエイリアスモック（checkUserHook は通過）
      $serviceMock = Mockery::mock('alias:App\\Services\\User\\HookService');
      $serviceMock->shouldReceive('checkUserHook')->andReturnNull();
      $serviceMock->shouldReceive('createHook')
         ->once()->andThrow(new Exception('DBエラー'));

      // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
      Log::shouldReceive('error')->once()->withAnyArgs();

      // 釣り針を保存するの為に、リクエスト送信
      $response = $this->from(route('user.masters.index', ['tab' => 'hooks']))
         ->post(route('user.hook.store'), $payload);

      // リダイレクトでエラーメッセージがフラッシュされていることを検証
      $response->assertRedirect(route('user.masters.index', ['tab' => 'hooks']));
      $response->assertSessionHas(['message' => '釣り針の追加に失敗しました', 'status' => 'error']);

      // レコードが保存されていないことを検証
      $this->assertDatabaseMissing('hooks', [
         'user_id' => $this->user->id,
         'name' => '失敗釣り針',
      ]);
   }

   // 釣り針を保存するテスト（AJAX想定のJSONレスポンス）
   public function testStoreAjaxHookController()
   {
      // リクエストデータを作成
      $payload = [
         'hook_name' => 'テスト釣り針',
      ];

      // 釣り針を保存するの為に、リクエスト送信
      $response = $this->post(route('user.hook.store.ajax'), $payload);

      // ステータスコード201 が返ることを検証
      $response->assertStatus(201);
      // JSONに、id とname キーが含まれる構造であることを検証
      $response->assertJsonStructure(['id', 'name']);
      // name が送信した釣り針と一致することを検証
      $response->assertJson(['name' => 'テスト釣り針']);

      // 釣り針が作成されていることを検証
      $this->assertDatabaseHas('hooks', [
         'user_id' => $this->user->id,
         'name' => 'テスト釣り針',
      ]);
   }

   // 釣り針を保存する時のエラーハンドリングをテスト（AJAX想定のJSONレスポンス）
   #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
   #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
   public function testErrorStoreAjaxHookController()
   {
      // リクエストデータを作成
      $payload = [
         'hook_name' => '失敗釣り針',
      ];

      // HookService::createHook が例外を投げるようにエイリアスモック（checkUserHook は通過）
      $hookServiceMock = Mockery::mock('alias:App\\Services\\User\\HookService');
      $hookServiceMock->shouldReceive('checkUserHook')->andReturnNull();
      $hookServiceMock->shouldReceive('createHook')
         ->once()->andThrow(new Exception('DBエラー'));

      // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
      Log::shouldReceive('error')->once()->withAnyArgs();

      // 釣り針を保存するの為に、リクエスト送信
      $response = $this->post(route('user.hook.store.ajax'), $payload);

      // ステータスコード500 が返ることを検証
      $response->assertStatus(500);
      // エラーがJSONで返却されていることを検証
      $response->assertJson([
         'message' => '釣り針の登録に失敗しました。',
         'status' => 'error',
      ]);

      // レコードが保存されていないことを検証
      $this->assertDatabaseMissing('hooks', [
         'user_id' => $this->user->id,
         'name' => '失敗釣り針',
      ]);
   }

   // 釣り針の編集画面が正しく表示されることをテスト
   public function testEditHookController()
   {
      // 自分の釣り針を1件作成
      $hook = Hook::factory()->create(['user_id' => $this->user->id, 'name' => '編集対象釣り針']);

      // 釣り針編集画面を表示する為に、リクエスト送信
      $response = $this->get(route('user.hook.edit', ['hook' => $hook->id]));

      // ステータスコード200（OK）であることを検証
      $response->assertOk();
      // 返却されるビューが期待通りであることを検証
      $response->assertViewIs('user.masters.partials.hooks.edit-hook');
      // ビューに渡される主要なデータ（選択釣り針）が存在することを検証
      $response->assertViewHas('hook');
   }

   // 釣り針名が正しく更新されることをテスト
   public function testUpdateHookController()
   {
      // 自分の釣り針を1件作成
      $hook = Hook::factory()->create(['user_id' => $this->user->id, 'name' => '旧釣針名']);

      // 更新用のリクエストデータを作成
      $payload = [
         'hookId' => $hook->id,
         'hook_name' => '新しい釣針名',
      ];

      // 釣り針名を更新する為に、リクエスト送信
      $response = $this->patch(route('user.hook.update'), $payload);

      // リダイレクトで成功メッセージがフラッシュされていることを検証
      $response->assertRedirect(route('user.masters.index', ['tab' => 'hooks']));
      $response->assertSessionHas(['message' => '釣り針名を更新しました。', 'status' => 'success']);

      // 釣り針名が正しく更新されていることを検証
      $this->assertDatabaseHas('hooks', [
         'id' => $hook->id,
         'name' => '新しい釣針名',
      ]);
   }

   // 釣り針名更新時のエラーハンドリングをテスト
   #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
   #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
   public function testErrorUpdateHookController()
   {
      // 自分の釣り針を1件作成
      $hook = Hook::factory()->create(['user_id' => $this->user->id, 'name' => '初期釣針名']);

      // 更新用のリクエストデータを作成
      $payload = [
         'hookId' => $hook->id,
         'hook_name' => '更新失敗釣針名',
      ];

      // HookService::updateHook が例外を投げるようにエイリアスモック（checkUserHook は通過）
      $hookServiceMock = Mockery::mock('alias:App\\Services\\User\\HookService');
      $hookServiceMock->shouldReceive('checkUserHook')->andReturnNull();
      $hookServiceMock->shouldReceive('updateHook')
         ->once()->andThrow(new Exception('DBエラー'));

      // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
      Log::shouldReceive('error')->once()->withAnyArgs();

      // 釣り針名を更新する為に、リクエスト送信
      $response = $this->from(route('user.hook.edit', ['hook' => $hook->id]))
         ->patch(route('user.hook.update'), $payload);

      // リダイレクトでエラーがフラッシュされていることを検証
      $response->assertRedirect(route('user.hook.edit', ['hook' => $hook->id]));
      $response->assertSessionHas(['message' => '釣り針名の更新に失敗しました。', 'status' => 'error']);

      // レコードが更新されていないことを検証
      $this->assertDatabaseMissing('hooks', [
         'id' => $hook->id,
         'name' => '更新失敗釣針名',
      ]);
   }

   // 釣り針が正しく削除されることをテスト
   public function testDestroyHookController()
   {
      // 自分の釣り針を1件作成
      $hook = Hook::factory()->create(['user_id' => $this->user->id]);

      // 釣り針を削除する為に、リクエスト送信
      $response = $this->delete(route('user.hook.destroy'), ['hookId' => $hook->id]);

      // リダイレクトで成功メッセージがフラッシュされていることを検証
      $response->assertRedirect();
      $response->assertSessionHas(['message' => '正常に釣り針を削除しました。', 'status' => 'success']);

      // 釣り針が削除されていることを検証
      $this->assertDatabaseMissing('hooks', ['id' => $hook->id]);
   }

   // 関連メモがある場合は削除できないことをテスト
   public function testErrorDestroyHookControllerRelatedMemo()
   {
      // 自分の釣り針を1件作成
      $hook = Hook::factory()->create(['user_id' => $this->user->id]);
      // 釣り針に関連するメモを1件作成し紐付ける
      $memo = Memo::factory()->create(['user_id' => $this->user->id]);
      $memo->hooks()->attach($hook->id);

      // 釣り針を削除する為に、リクエスト送信
      $response = $this->delete(route('user.hook.destroy'), ['hookId' => $hook->id]);

      // リダイレクトでエラーメッセージがフラッシュされていることを検証
      $response->assertRedirect();
      $response->assertSessionHas(['message' => '関連データのため削除できません。', 'status' => 'error']);

      // レコードが削除されていないことを確認
      $this->assertDatabaseHas('hooks', ['id' => $hook->id]);
   }

   // 釣り針削除時のエラーハンドリングをテスト
   #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
   #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
   public function testErrorDestroyHookControllerException()
   {
      // HookモデルのavailableSelectHookが例外を投げるようにエイリアスモック
      $hookAliasMock = Mockery::mock('alias:App\\Models\\Hook');
      $hookAliasMock->shouldReceive('availableSelectHook')->andThrow(new Exception('DBエラー'));

      // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
      Log::shouldReceive('error')->once()->withAnyArgs();

      // 実データを作らず、取得時に例外が発生する想定で、釣り針削除のリクエストを送信
      $response = $this->delete(route('user.hook.destroy'), ['hookId' => 9999]);

      // リダイレクトでエラーメッセージがフラッシュされていることを検証
      $response->assertRedirect();
      $response->assertSessionHas(['message' => '釣り針の削除に失敗しました。', 'status' => 'error']);
   }
}
