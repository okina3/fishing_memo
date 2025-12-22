<?php

namespace Tests\User\Feature\Services;

use App\Models\Bait;
use App\Models\User;
use App\Services\User\BaitService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\User\TestCase;

class BaitServiceTest extends TestCase
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

   // 別ユーザーのエサを見られなくするメソッドのテスト
   public function testCheckUserBait()
   {
      // 2人目のユーザーのエサを作成
      $secondaryBait = Bait::factory()->create(['user_id' => $this->secondaryUser->id]);

      // リクエストを作成し、ルートパラメーターをバインド
      $request = Request::create('/baits/' . $secondaryBait->id);
      $request->setRouteResolver(function () use ($request) {
         return (new Route('GET', '/baits/{bait}', []))->bind($request);
      });

      // 異常なユーザーのエサのアクセスは、例外の発生を期待（404エラー）
      $this->expectException(NotFoundHttpException::class);
      // エサの所有者を確認するサービスメソッドを実行
      BaitService::checkUserBait($request);
   }

   // 新規エサの保存テスト
   public function testCreateBait()
   {
      // 新しいエサ名を用意してサービス経由で保存
      $name = 'テストエサ';
      $bait = BaitService::createBait($name);

      // 作成されたエサがDBに存在するかを確認
      $this->assertDatabaseHas('baits', [
         'id' => $bait->id,
         'name' => $name,
         'user_id' => $this->user->id,
      ]);
   }

   // 既存エサの更新テスト
   public function testUpdateBait()
   {
      // 既に存在する自分のエサを作成
      $bait = Bait::factory()->create(['user_id' => $this->user->id, 'name' => 'old']);

      // サービスを使って名前を更新
      $updated = BaitService::updateBait($bait->id, 'updated_name');

      // 更新が DB に反映されていることを確認
      $this->assertDatabaseHas('baits', [
         'id' => $bait->id,
         'name' => 'updated_name',
      ]);

      // 戻り値が更新後の値を含むことを確認
      $this->assertEquals('updated_name', $updated->name);
   }

   // 選択したメモに紐づいた、エサのNameを配列で取得するメソッドのテスト
   public function testGetMemoBaitsName()
   {
      // 2件の自分のエサを作成
      $baits = Bait::factory()->count(2)->create(['user_id' => $this->user->id]);

      // サービスメソッドを実行して名前配列を取得
      $names = BaitService::getMemoBaitsName($baits);

      // 作成したエサの名前配列と一致することを確認
      $this->assertEquals($baits->pluck('name')->toArray(), $names);
   }
}
