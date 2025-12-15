<?php

namespace Tests\User\Feature\Services;

use App\Models\Spot;
use App\Models\User;
use App\Services\SpotService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\User\TestCase;

class SpotServiceTest extends TestCase
{
   use RefreshDatabase;

   private User $user;
   private User $secondaryUser;

   // テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
   protected function setUp(): void
   {
      // 親クラスのsetUpメソッドを呼び出し
      parent::setUp();
      // ユーザーを作成
      $this->user = User::factory()->create();
      // 2人目の別のユーザーを作成
      $this->secondaryUser = User::factory()->create();
      // 認証済みのユーザーを返す
      $this->actingAs($this->user, 'users');
   }

   // 別ユーザーの釣り場を見られなくするメソッドのテスト
   public function testCheckUserSpot()
   {
      // 2人目のユーザーの釣り場を作成
      $secondarySpot = Spot::factory()->create(['user_id' => $this->secondaryUser->id]);

      // リクエストを作成
      $request = Request::create('/spots/' . $secondarySpot->id);
      // リクエストのルートを設定
      $request->setRouteResolver(function () use ($request) {
         // 新しいルートオブジェクトを作成し、リクエストにバインド
         return (new Route('GET', '/spots/{spot}', []))->bind($request);
      });

      // 異常なユーザーの釣り場のアクセスは、例外の発生を期待（404エラー）
      $this->expectException(NotFoundHttpException::class);
      // 釣り場の所有者を確認するサービスメソッドを実行
      SpotService::checkUserSpot($request);
   }

   // 新規釣り場の保存テスト
   public function testCreateSpot()
   {
      // 新しい釣り場名を用意してサービス経由で保存
      $name = 'テスト釣り場';
      $spot = SpotService::createSpot($name);

      // DB に保存されていることを検証
      $this->assertDatabaseHas('spots', [
         'id' => $spot->id,
         'name' => $name,
         'user_id' => $this->user->id,
      ]);
   }

   // 既存釣り場の更新テスト
   public function testUpdateSpot()
   {
      // 既に存在する自分の釣り場を作成
      $spot = Spot::factory()->create(['user_id' => $this->user->id, 'name' => 'old']);

      // サービスを使って名前を更新
      $updated = SpotService::updateSpot($spot->id, 'updated_name');

      // 更新が DB に反映されていることを確認
      $this->assertDatabaseHas('spots', [
         'id' => $spot->id,
         'name' => 'updated_name',
      ]);
      $this->assertEquals('updated_name', $updated->name);
   }
}
