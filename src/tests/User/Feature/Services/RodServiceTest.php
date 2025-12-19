<?php

namespace Tests\User\Feature\Services;

use App\Models\Rod;
use App\Models\User;
use App\Services\RodService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\User\TestCase;

class RodServiceTest extends TestCase
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

   // 別ユーザーの釣り竿を見られなくするメソッドのテスト
   public function testCheckUserRod()
   {
      // 2人目のユーザーの釣り竿を作成
      $secondaryRod = Rod::factory()->create(['user_id' => $this->secondaryUser->id]);

      // リクエストを作成
      $request = Request::create('/rods/' . $secondaryRod->id);
      // リクエストのルートを設定
      $request->setRouteResolver(function () use ($request) {
         // 新しいルートオブジェクトを作成し、リクエストにバインド
         return (new Route('GET', '/rods/{rod}', []))->bind($request);
      });

      // 異常なユーザーの釣り竿のアクセスは、例外の発生を期待（404エラー）
      $this->expectException(NotFoundHttpException::class);
      // 釣り竿の所有者を確認するサービスメソッドを実行
      RodService::checkUserRod($request);
   }

   // 新規釣り竿の保存テスト
   public function testCreateRod()
   {
      // 新しい釣り竿名を用意してサービス経由で保存
      $name = 'テスト竿';
      $rod = RodService::createRod($name);

      // DB に保存されていることを検証
      $this->assertDatabaseHas('rods', [
         'id' => $rod->id,
         'name' => $name,
         'user_id' => $this->user->id,
      ]);
   }

   // 既存釣り竿の更新テスト
   public function testUpdateRod()
   {
      // 既に存在する自分の釣り竿を作成
      $rod = Rod::factory()->create(['user_id' => $this->user->id, 'name' => 'old']);

      // サービスを使って名前を更新
      $updated = RodService::updateRod($rod->id, 'updated_name');

      // DB に更新されていることを検証
      $this->assertDatabaseHas('rods', [
         'id' => $rod->id,
         'name' => 'updated_name',
      ]);
      $this->assertEquals('updated_name', $updated->name);
   }

   // 選択したメモに紐づいた釣り竿データを配列で取得するメソッドのテスト
   public function testGetMemoRodsResults()
   {
      // 2件の釣り竿を作成
      $rods = Rod::factory()->count(2)->create(['user_id' => $this->user->id]);

      // pivot 情報を手動で付与
      $rods[0]->pivot = (object)[
         'main_line' => 2.5,
      ];
      $rods[1]->pivot = (object)[
         'main_line' => 3.0,
      ];

      // サービスメソッドを実行
      $results = RodService::getMemoRodsResults($rods);

      // 期待される配列を作成
      $expected = [
         ['name' => $rods[0]->name, 'main_line' => 2.5],
         ['name' => $rods[1]->name, 'main_line' => 3.0],
      ];

      // サービスが返す配列が期待したデータの配列と一致することを確認
      $this->assertEquals($expected, $results);
   }
}
