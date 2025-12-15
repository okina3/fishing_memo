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

   // 選択したメモに紐づいた釣り場データを配列で取得するメソッドのテスト
   public function testGetMemoSpotsResults()
   {
      // 2件の自分の釣り場を作成
      $spots = Spot::factory()->count(2)->create(['user_id' => $this->user->id]);

      // pivot 情報を手動で付与
      $spots[0]->pivot = (object)[
         'river_flow' => 'あり',
         'turbidity' => 'クリア',
         'water_level' => 1.5,
         'water_temp' => 20,
      ];
      $spots[1]->pivot = (object)[
         'river_flow' => 'なし',
         'turbidity' => '濁り',
         'water_level' => 0.5,
         'water_temp' => 15,
      ];

      // サービスメソッドを実行
      $results = SpotService::getMemoSpotsResults($spots);

      // 期待される配列を作成
      $expected = [
         [
            'name' => $spots[0]->name,
            'river_flow' => 'あり',
            'turbidity' => 'クリア',
            'water_level' => 1.5,
            'water_temp' => 20,
         ],
         [
            'name' => $spots[1]->name,
            'river_flow' => 'なし',
            'turbidity' => '濁り',
            'water_level' => 0.5,
            'water_temp' => 15,
         ],
      ];

      // サービスが返す配列が期待したデータの配列と一致することを確認
      $this->assertEquals($expected, $results);
   }
}
