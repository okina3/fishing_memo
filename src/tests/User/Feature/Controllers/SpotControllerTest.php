<?php

namespace Tests\User\Feature\Controllers;

use App\Models\Memo;
use App\Models\Spot;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\User\TestCase;

class SpotControllerTest extends TestCase
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

   // 釣り場が正しく保存されることをテスト（AJAX想定のJSONレスポンス）
   public function testStoreSpotController()
   {
      // リクエストデータを作成
      $payload = [
         'spot_name' => 'テスト釣り場',
      ];

      // 釣り場を保存するの為に、リクエスト送信
      $response = $this->post(route('user.spot.store'), $payload);

      // ステータスコード201 が返ることを検証
      $response->assertStatus(201);
      // JSONに、id とname キーが含まれる構造であることを検証
      $response->assertJsonStructure(['id', 'name']);
      // name が送信した釣り場と一致することを検証
      $response->assertJson(['name' => 'テスト釣り場']);

      // 釣り場が作成されていることを検証
      $this->assertDatabaseHas('spots', [
         'user_id' => $this->user->id,
         'name' => 'テスト釣り場',
      ]);
   }

   // 釣り場保存時のエラーハンドリングをテスト
   #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
   #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
   public function testErrorStoreSpotController()
   {
      // リクエストデータを作成
      $payload = [
         'spot_name' => '失敗する釣り場',
      ];

      // SpotService::createSpot が例外を投げるようにエイリアスモック（checkUserSpot は通過）
      $spotServiceMock = Mockery::mock('alias:App\\Services\\SpotService');
      $spotServiceMock->shouldReceive('checkUserSpot')->andReturnNull();
      $spotServiceMock->shouldReceive('createSpot')
         ->once()->andThrow(new Exception('DBエラー'));

      // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
      Log::shouldReceive('error')->once()->withAnyArgs();

      // 釣り場を保存するの為に、リクエスト送信
      $response = $this->post(route('user.spot.store'), $payload);

      // ステータスコード500 が返ることを検証
      $response->assertStatus(500);
      // エラーがフラッシュされていることを検証
      $response->assertJson([
         'message' => '釣り場の登録に失敗しました。',
         'status' => 'error',
      ]);

      // レコードが保存されていないことを検証
      $this->assertDatabaseMissing('spots', [
         'user_id' => $this->user->id,
         'name' => '失敗する釣り場',
      ]);
   }

   // 釣り場の編集画面が正しく表示されることをテスト
   public function testEditSpotController()
   {
      // 自分の釣り場を1件作成
      $spot = Spot::factory()->create(['user_id' => $this->user->id, 'name' => '編集対象スポット']);

      // 釣り場編集画面を表示する為に、リクエスト送信
      $response = $this->get(route('user.spot.edit', ['spot' => $spot->id]));

      // ステータスコード200（OK）であることを検証
      $response->assertOk();
      // 返却されるビューが期待通り（user.masters.edit-spot）であることを検証
      $response->assertViewIs('user.masters.edit-spot');
      // ビューに渡される主要なデータ（選択釣り場）が存在することを検証
      $response->assertViewHas('spot');
   }

   // 釣り場名が正しく更新されることをテスト
   public function testUpdateSpotController()
   {
      // 自分の釣り場を1件作成
      $spot = Spot::factory()->create(['user_id' => $this->user->id, 'name' => '旧スポット名']);

      // 更新用のリクエストデータを作成
      $payload = [
         'spotId' => $spot->id,
         'spot_name' => '新しいスポット名',
      ];

      // 釣り場を更新するの為に、リクエスト送信
      $response = $this->patch(route('user.spot.update'), $payload);

      // リダイレクトで成功メッセージがフラッシュされていることを検証
      $response->assertRedirect(route('user.masters.index', ['tab' => 'spots']));
      $response->assertSessionHas(['message' => '釣り場名を更新しました。', 'status' => 'success']);

      // メモが更新されていることを検証
      $this->assertDatabaseHas('spots', [
         'id' => $spot->id,
         'name' => '新しいスポット名',
      ]);
   }

   // 釣り場名更新時のエラーハンドリングをテスト
   #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
   #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
   public function testErrorUpdateSpotController()
   {
      // 自分の釣り場を1件作成
      $spot = Spot::factory()->create(['user_id' => $this->user->id, 'name' => '初期スポット名']);

      // 更新用のリクエストデータを作成
      $payload = [
         'spotId' => $spot->id,
         'spot_name' => '更新失敗スポット名',
      ];

      // SpotService::updateSpot が例外を投げるようにエイリアスモック（checkUserSpot は通過）
      $spotServiceMock = Mockery::mock('alias:App\\Services\\SpotService');
      $spotServiceMock->shouldReceive('checkUserSpot')->andReturnNull();
      $spotServiceMock->shouldReceive('updateSpot')
         ->once()->andThrow(new Exception('DBエラー'));

      // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
      Log::shouldReceive('error')->once()->withAnyArgs();

      // 釣り場を更新するの為に、リクエスト送信
      $response = $this->from(route('user.spot.edit', ['spot' => $spot->id]))
         ->patch(route('user.spot.update'), $payload);

      // リダイレクトでエラーがフラッシュされていることを検証
      $response->assertRedirect(route('user.spot.edit', ['spot' => $spot->id]));
      $response->assertSessionHas(['message' => '釣り場名の更新に失敗しました。', 'status' => 'error']);

      // レコードが更新されていないことを検証
      $this->assertDatabaseMissing('spots', [
         'id' => $spot->id,
         'name' => '更新失敗スポット名',
      ]);
   }

   // 釣り場が正しく削除されることをテスト
   public function testDestroySpotController()
   {
      // 自分の釣り場を1件作成
      $spot = Spot::factory()->create(['user_id' => $this->user->id]);

      // 釣り場を削除する為に、リクエストを送信
      $response = $this->delete(route('user.spot.destroy'), ['spotId' => $spot->id]);

      // リダイレクトで成功メッセージがフラッシュされていることを検証
      $response->assertRedirect();
      $response->assertSessionHas(['message' => '正常に釣り場を削除しました。', 'status' => 'success']);

      // 釣り場が削除されたことを確認
      $this->assertDatabaseMissing('spots', [
         'id' => $spot->id,
      ]);
   }

   // 関連メモがある場合は削除できないことをテスト
   public function testErrorDestroySpotControllerRelatedMemo()
   {
      // 自分の釣り場を1件作成
      $spot = Spot::factory()->create(['user_id' => $this->user->id]);

      // 釣り場に関連するメモを1件作成
      Memo::factory()->create(['user_id' => $this->user->id, 'spot_id' => $spot->id]);

      // 釣り場を削除する為に、リクエストを送信
      $response = $this->delete(route('user.spot.destroy'), ['spotId' => $spot->id]);

      // リダイレクトでエラーメッセージがフラッシュされていることを検証
      $response->assertRedirect();
      $response->assertSessionHas(['message' => '関連データのため削除できません。', 'status' => 'error']);

      // レコードが削除されていないことを確認
      $this->assertDatabaseHas('spots', [
         'id' => $spot->id,
      ]);
   }

   // 釣り場削除時の例外ハンドリングをテスト
   #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
   #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
   public function testErrorDestroySpotControllerException()
   {
      // SpotモデルのavailableSelectSpotメソッドが例外を投げるようにエイリアスモック
      $spotAliasMock = Mockery::mock('alias:App\\Models\\Spot');
      $spotAliasMock->shouldReceive('availableSelectSpot')->andThrow(new Exception('DBエラー'));

      // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
      Log::shouldReceive('error')->once()->withAnyArgs();

      // 実データを作らず、取得時に例外が発生する想定で、釣り場削除のリクエストを送信
      $response = $this->delete(route('user.spot.destroy'), ['spotId' => 9999]);

      // リダイレクトでエラーがフラッシュされていることを検証
      $response->assertRedirect();
      $response->assertSessionHas(['message' => '釣り場の削除に失敗しました。', 'status' => 'error']);
   }
}
