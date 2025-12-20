<?php

namespace Tests\User\Feature\Services;

use App\Models\Hook;
use App\Models\User;
use App\Services\HookService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\User\TestCase;

class HookServiceTest extends TestCase
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

   // 別ユーザーの釣り針を見られなくするメソッドのテスト
   public function testCheckUserHook()
   {
      // 2人目のユーザーの釣り針を作成
      $secondaryHook = Hook::factory()->create(['user_id' => $this->secondaryUser->id]);

      // リクエストを作成
      $request = Request::create('/hooks/' . $secondaryHook->id);
      // リクエストのルートを設定
      $request->setRouteResolver(function () use ($request) {
         // 新しいルートオブジェクトを作成し、リクエストにバインド
         return (new Route('GET', '/hooks/{hook}', []))->bind($request);
      });

      // 異常なユーザーの釣り針のアクセスは、例外の発生を期待（404エラー）
      $this->expectException(NotFoundHttpException::class);
      // 釣り針の所有者を確認するサービスメソッドを実行
      HookService::checkUserHook($request);
   }

   // 新規釣り針の保存テスト
   public function testCreateHook()
   {
      // 新しい釣り針名を用意してサービス経由で保存
      $name = 'テスト釣り針';
      $hook = HookService::createHook($name);

      // DB に保存されていることを検証
      $this->assertDatabaseHas('hooks', [
         'id' => $hook->id,
         'name' => $name,
         'user_id' => $this->user->id,
      ]);
   }

   // 既存釣り針の更新テスト
   public function testUpdateHook()
   {
      // 既に存在する自分の釣り針を作成
      $hook = Hook::factory()->create(['user_id' => $this->user->id, 'name' => 'old']);

      // サービスを使って名前を更新
      $updated = HookService::updateHook($hook->id, 'updated_name');

      // 更新が DB に反映されていることを確認
      $this->assertDatabaseHas('hooks', [
         'id' => $hook->id,
         'name' => 'updated_name',
      ]);
      $this->assertEquals('updated_name', $updated->name);
   }

   // 選択したメモに紐づいた釣り針データを配列で取得するメソッドのテスト
   public function testGetMemoHooksResults()
   {
      // 2件の釣り針を作成
      $hooks = Hook::factory()->count(2)->create(['user_id' => $this->user->id]);

      // pivot 情報を手動で付与
      $hooks[0]->pivot = (object)[
         'leader_size' => 1.5,
         'leader_upper_cm' => 30,
         'leader_lower_cm' => 20,
      ];
      $hooks[1]->pivot = (object)[
         'leader_size' => 2.0,
         'leader_upper_cm' => 40,
         'leader_lower_cm' => 25,
      ];

      // サービスメソッドを実行
      $results = HookService::getMemoHooksResults($hooks);

      // 期待される配列を作成
      $expected = [
         [
            'name' => $hooks[0]->name,
            'leader_size' => 1.5,
            'leader_upper_cm' => 30,
            'leader_lower_cm' => 20,
         ],
         [
            'name' => $hooks[1]->name,
            'leader_size' => 2.0,
            'leader_upper_cm' => 40,
            'leader_lower_cm' => 25,
         ],
      ];

      // サービスが返す配列が期待したデータの配列と一致することを確認
      $this->assertEquals($expected, $results);
   }
}
