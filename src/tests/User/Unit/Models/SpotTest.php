<?php

namespace Tests\User\Unit\Models;

use App\Models\Memo;
use App\Models\Spot;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\User\TestCase;

class SpotTest extends TestCase
{
   use RefreshDatabase;

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

   // 釣り場を作成するヘルパーメソッド
   private function createSpots(int $count): Collection
   {
      // 指定された数の釣り場を、現在のユーザーに関連付けて作成する
      return Spot::factory()->count($count)->create(['user_id' => $this->user->id]);
   }

   // 釣り場にメモを作成して関連付けるヘルパーメソッド
   private function createMemosForSpot(Spot $spot, int $count): Collection
   {
      // メモを作成し、釣り場を関連付け
      $memos = Memo::factory()->count($count)->create([
         'spot_id' => $spot->id,
         'user_id' => $this->user->id,
      ]);

      // リレーションを最新化し（テストの安定化のため）
      $spot->load('memos');

      // 作成されたメモのコレクションを返す
      return $memos;
   }

   // 基本的なリレーションが、正しく機能しているかのテスト
   public function testSpotAttributesAndRelations()
   {
      // 1件の釣り場を作成
      $spot = $this->createSpots(1)->first();
      // 釣り場に2件のメモを関連付け
      $createdMemos = $this->createMemosForSpot($spot, 2);

      // 釣り場とメモのリレーションが、正しいインスタンスであることを確認
      $this->assertInstanceOf(HasMany::class, $spot->memos());

      // 作成した関連付けられたメモのID配列が、作成した釣り場に紐づいたメモのID配列と、一致しているかを確認（順序非依存）
      $this->assertEqualsCanonicalizing($createdMemos->pluck('id')->toArray(), $spot->memos->pluck('id')->toArray());

      // 釣り場とユーザーのリレーションが、正しいインスタンスであることを確認
      $this->assertInstanceOf(BelongsTo::class, $spot->user());
      // 自分のユーザーのIDが、作成した釣り場に紐づいたユーザーのIDと、一致しているかを確認
      $this->assertEquals($this->user->id, $spot->user->id);
   }

   // 自分自身の全ての釣り場を、取得するスコープのテスト
   public function testAvailableAllSpotsScope()
   {
      // 3件の釣り場を作成
      $spots = $this->createSpots(3);
      // 全ての釣り場を取得
      $allSpots = Spot::availableAllSpots()->get();

      // 作成した釣り場のIDの配列が、取得した釣り場のIDの配列と、一致するか確認（順序非依存）
      $this->assertEqualsCanonicalizing($spots->pluck('id')->toArray(), $allSpots->pluck('id')->toArray());
   }

   // 自分自身の選択した釣り場を、取得するスコープのテスト
   public function testAvailableSelectSpotScope()
   {
      // 1件の釣り場を作成
      $spot = $this->createSpots(1)->first();
      // 選択した釣り場を取得
      $selectedSpot = Spot::availableSelectSpot($spot->id)->first();

      // 作成した釣り場のIDが、取得した釣り場のIDと、一致するか確認
      $this->assertEquals($spot->id, $selectedSpot->id);
   }

   // 検索キーワードによる釣り場の検索スコープのテスト
   public function testSearchKeywordScope()
   {
      // 既存データをクリア
      Spot::query()->delete();

      // 3件の釣り場のデータを作成
      Spot::factory()->create(['name' => '利根川', 'user_id' => $this->user->id]);
      Spot::factory()->create(['name' => '荒川', 'user_id' => $this->user->id]);
      Spot::factory()->create(['name' => '琵琶湖', 'user_id' => $this->user->id]);

      // キーワード「荒」で、釣り場を検索
      $result = Spot::searchKeyword('荒')->get();
      $this->assertCount(1, $result);
      // 検索結果の最初の要素の釣り場に「荒」が含まれているかを確認
      $this->assertStringContainsString('荒', $result->first()->name);
   }
}
