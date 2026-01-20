<?php

namespace Tests\User\Feature\Services;

use App\Models\FishName;
use App\Models\User;
use App\Services\User\FishNameService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\User\TestCase;

class FishNameServiceTest extends TestCase
{
   use RefreshDatabase;

   private User $user;
   private User $secondaryUser;

   // テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
   protected function setUp(): void
   {
      parent::setUp();
      // ユーザーを作成
      $this->user = User::factory()->create();
      // 2人目の別のユーザーを作成
      $this->secondaryUser = User::factory()->create();
      // 認証済みのユーザーを返す
      $this->actingAs($this->user, 'users');
   }

   // 別ユーザーの魚名を見られなくするメソッドのテスト
   public function testCheckUserFishNameForbidden()
   {
      // 2人目のユーザーの魚名を作成（所有者は別ユーザー）
      $secondaryFish = FishName::factory()->create(['user_id' => $this->secondaryUser->id]);

      // リクエストを作成し、ルートパラメーターをバインド
      $request = Request::create('/fishNames/' . $secondaryFish->id);
      $request->setRouteResolver(function () use ($request) {
         return (new Route('GET', '/fishNames/{fishName}', []))->bind($request);
      });

      // 異常なユーザーの魚名のアクセスは、例外の発生を期待（404エラー）
      $this->expectException(NotFoundHttpException::class);
      // 魚名の所有者を確認するサービスメソッドを実行
      FishNameService::checkUserFishName($request);
   }

   // 新規魚名の保存テスト
   public function testCreateFishName()
   {
      // 新しい魚名を用意してサービス経由で保存
      $name = 'テスト魚名';
      $fish = FishNameService::createFishName($name);

      // 作成された魚名がDBに存在するかを確認
      $this->assertDatabaseHas('fish_names', [
         'id' => $fish->id,
         'name' => $name,
         'user_id' => $this->user->id,
      ]);
   }

   // 既存魚名の更新テスト
   public function testUpdateFishName()
   {
      // 既に存在する自分の魚名を作成
      $fish = FishName::factory()->create(['user_id' => $this->user->id, 'name' => 'old']);

      // サービスを使って名前を更新
      $updated = FishNameService::updateFishName($fish->id, 'updated_name');

      // 更新が DB に反映されていることを確認
      $this->assertDatabaseHas('fish_names', [
         'id' => $fish->id,
         'name' => 'updated_name',
      ]);

      // 戻り値が更新後の値を含むことを確認
      $this->assertEquals('updated_name', $updated->name);
   }

   // 選択したメモに紐づいた釣果データ（name/count/length）を配列で取得するメソッドのテスト
   public function testGetMemoFishResults()
   {
      // 2件の自分の魚名を作成
      $fishNames = FishName::factory()->count(2)->create(['user_id' => $this->user->id]);

      // pivot 情報を手動で付与
      $fishNames[0]->pivot = (object)[
         'count' => 3, 
         'length' => 30
      ];
      $fishNames[1]->pivot = (object)[
         'count' => 1, 
         'length' => 10
      ];

      // サービスメソッドを実行
      $results = FishNameService::getMemoFishResults($fishNames);

      // 期待する配列を作成
      $expected = [
         [
            'name' => $fishNames[0]->name, 
            'count' => 3, 
            'length' => 30
         ],
         [
            'name' => $fishNames[1]->name, 
            'count' => 1, 
            'length' => 10
         ],
      ];

      // サービスが返す配列が期待した釣果データの配列と一致することを確認
      $this->assertEquals($expected, $results);
   }
}
