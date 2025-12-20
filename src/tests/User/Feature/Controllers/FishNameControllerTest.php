<?php

namespace Tests\User\Feature\Controllers;

use App\Models\FishName;
use App\Models\Memo;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\User\TestCase;

class FishNameControllerTest extends TestCase
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

   // 魚名を保存するテスト
   public function testStoreFishNameController()
   {
      // リクエストデータを作成
      $payload = ['fish_name' => 'マスター追加魚名'];

      // 魚名を保存するの為に、リクエスト送信
      $response = $this->post(route('user.fish-name.store'), $payload);

      // リダイレクトで成功メッセージがフラッシュされていることを検証
      $response->assertRedirect(route('user.masters.index', ['tab' => 'fishNames']));
      $response->assertSessionHas(['message' => '魚名を追加しました。', 'status' => 'success']);

      // 魚名が作成されていることを検証
      $this->assertDatabaseHas('fish_names', [
         'user_id' => $this->user->id,
         'name' => 'マスター追加魚名',
      ]);
   }

   // 魚名を保存する時のエラーハンドリングをテスト
   #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
   #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
   public function testErrorStoreFishNameController()
   {
      // リクエストデータを作成
      $payload = ['fish_name' => '失敗魚名'];

      // FishNameService::createFishName が例外を投げるようにエイリアスモック（checkUserFishName は通過）
      $serviceMock = Mockery::mock('alias:App\\Services\\FishNameService');
      $serviceMock->shouldReceive('checkUserFishName')->andReturnNull();
      $serviceMock->shouldReceive('createFishName')
         ->once()->andThrow(new Exception('DBエラー'));

      // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
      Log::shouldReceive('error')->once()->withAnyArgs();

      // 魚名を保存するの為に、リクエスト送信
      $response = $this->from(route('user.masters.index', ['tab' => 'fishNames']))
         ->post(route('user.fish-name.store'), $payload);

      // リダイレクトでエラーメッセージがフラッシュされていることを検証
      $response->assertRedirect(route('user.masters.index', ['tab' => 'fishNames']));
      $response->assertSessionHas(['message' => '魚名の追加に失敗しました', 'status' => 'error']);

      // レコードが保存されていないことを検証
      $this->assertDatabaseMissing('fish_names', [
         'user_id' => $this->user->id,
         'name' => '失敗魚名',
      ]);
   }

   // 魚名の編集画面が正しく表示されることをテスト
   public function testEditFishNameController()
   {
      // 自分の魚名を1件作成
      $fish = FishName::factory()->create(['user_id' => $this->user->id, 'name' => '編集対象魚名']);

      // 魚名編集画面を表示する為に、リクエスト送信
      $response = $this->get(route('user.fish-name.edit', ['fishName' => $fish->id]));

      // ステータスコード200（OK）であることを検証
      $response->assertOk();
      // 返却されるビューが期待通り（user.masters.fish-names.edit-fish-name）であることを検証
      $response->assertViewIs('user.masters.fish-names.edit-fish-name');
      // ビューに渡される主要なデータ（編集対象魚名）が存在することを検証
      $response->assertViewHas('fish_name');
   }

   // 魚名が正しく更新されることをテスト
   public function testUpdateFishNameController()
   {
      // 自分の魚名を1件作成
      $fish = FishName::factory()->create(['user_id' => $this->user->id, 'name' => '旧魚名']);

      // 更新用のリクエストデータを作成
      $payload = [
         'fishNameId' => $fish->id,
         'fish_name' => '新しい魚名',
      ];

      // 魚名を更新する為に、リクエスト送信
      $response = $this->patch(route('user.fish-name.update'), $payload);

      // リダイレクトで成功メッセージがフラッシュされていることを検証
      $response->assertRedirect(route('user.masters.index', ['tab' => 'fishNames']));
      $response->assertSessionHas(['message' => '魚名を更新しました。', 'status' => 'success']);

      // 魚名が更新されていることを検証
      $this->assertDatabaseHas('fish_names', [
         'id' => $fish->id,
         'name' => '新しい魚名',
      ]);
   }

   // 魚名更新時のエラーハンドリングをテスト
   #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
   #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
   public function testErrorUpdateFishNameController()
   {
      // 自分の魚名を1件作成
      $fish = FishName::factory()->create(['user_id' => $this->user->id, 'name' => '初期魚名']);

      // 更新用のリクエストデータを作成
      $payload = [
         'fishNameId' => $fish->id,
         'fish_name' => '更新失敗魚名',
      ];

      // FishNameService::updateFishName が例外を投げるようにエイリアスモック（checkUserFishName は通過）
      $serviceMock = Mockery::mock('alias:App\\Services\\FishNameService');
      $serviceMock->shouldReceive('checkUserFishName')->andReturnNull();
      $serviceMock->shouldReceive('updateFishName')
         ->once()->andThrow(new Exception('DBエラー'));

      // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
      Log::shouldReceive('error')->once()->withAnyArgs();

      // 魚名を更新する為に、リクエスト送信
      $response = $this->from(route('user.fish-name.edit', ['fishName' => $fish->id]))
         ->patch(route('user.fish-name.update'), $payload);

      // リダイレクトでエラーがフラッシュされていることを検証
      $response->assertRedirect(route('user.fish-name.edit', ['fishName' => $fish->id]));
      $response->assertSessionHas(['message' => '魚名の更新に失敗しました。', 'status' => 'error']);

      // レコードが更新されていないことを検証
      $this->assertDatabaseMissing('fish_names', [
         'id' => $fish->id,
         'name' => '更新失敗魚名',
      ]);
   }

   // 魚名が正しく削除されることをテスト
   public function testDestroyFishNameController()
   {
      // 自分の魚名を1件作成
      $fish = FishName::factory()->create(['user_id' => $this->user->id]);

      // 魚名を削除する為に、リクエストを送信
      $response = $this->delete(route('user.fish-name.destroy'), ['fishNameId' => $fish->id]);

      // リダイレクトで成功メッセージがフラッシュされていることを検証
      $response->assertRedirect();
      $response->assertSessionHas(['message' => '正常に魚名を削除しました。', 'status' => 'success']);

      // 魚名が削除されたことを確認
      $this->assertDatabaseMissing('fish_names', [
         'id' => $fish->id,
      ]);
   }

   // 関連メモがある場合は削除できないことをテスト
   public function testErrorDestroyFishNameControllerRelatedMemo()
   {
      // 自分の魚名を1件作成
      $fish = FishName::factory()->create(['user_id' => $this->user->id]);

      // 魚名に関連するメモを1件作成し紐付ける
      $memo = Memo::factory()->create(['user_id' => $this->user->id]);
      $memo->fish_names()->attach($fish->id, ['count' => 1, 'length' => 10]);

      // 魚名を削除する為に、リクエストを送信
      $response = $this->delete(route('user.fish-name.destroy'), ['fishNameId' => $fish->id]);

      // リダイレクトでエラーメッセージがフラッシュされていることを検証
      $response->assertRedirect();
      $response->assertSessionHas(['message' => '関連データのため削除できません。', 'status' => 'error']);

      // 魚名が削除されていないことを確認
      $this->assertDatabaseHas('fish_names', [
         'id' => $fish->id,
      ]);
   }

   // 魚名削除時の例外ハンドリングをテスト
   #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
   #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
   public function testErrorDestroyFishNameControllerException()
   {
      // FishNameモデルのavailableSelectFishName メソッドが例外を投げるようにエイリアスモック
      $alias = Mockery::mock('alias:App\\Models\\FishName');
      $alias->shouldReceive('availableSelectFishName')->andThrow(new Exception('DBエラー'));

      // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
      Log::shouldReceive('error')->once()->withAnyArgs();

      // 実データを作らず、取得時に例外が発生する想定で、魚名削除のリクエストを送信
      $response = $this->delete(route('user.fish-name.destroy'), ['fishNameId' => 99999]);

      // リダイレクトでエラーがフラッシュされていることを検証
      $response->assertRedirect();
      $response->assertSessionHas(['message' => '魚名の削除に失敗しました。', 'status' => 'error']);
   }
}
