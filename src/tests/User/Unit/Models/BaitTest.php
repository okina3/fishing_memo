<?php

namespace Tests\User\Unit\Models;

use App\Models\Bait;
use App\Models\Memo;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\User\TestCase;

class BaitTest extends TestCase
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

   // エサを作成するヘルパーメソッド
   private function createBaits(int $count): Collection
   {
      // 指定された数のエサを、現在のユーザーに関連付けて作成する
      return Bait::factory()->count($count)->create(['user_id' => $this->user->id]);
   }

   // エサにメモを関連付けるヘルパーメソッド
   private function attachMemos(Bait $bait, int $memoCount): Collection
   {
      // メモを作成し、エサに関連付け
      $memos = Memo::factory()->count($memoCount)->create();
      $bait->memos()->attach($memos->pluck('id')->toArray());

      // リレーションを最新化し（テストの安定化のため）
      $bait->load('memos');

      // 作成されたメモのコレクションを返す
      return $memos;
   }

   // 基本的なリレーションが、正しく機能しているかのテスト
   public function testBaitAttributesAndRelations()
   {
      // 1件のエサを作成
      $bait = $this->createBaits(1)->first();
      // エサに2件のメモを関連付け
      $attachedMemos = $this->attachMemos($bait, 2);

      // エサとメモのリレーションが、正しいインスタンスであることを確認
      $this->assertInstanceOf(BelongsToMany::class, $bait->memos());
      // 作成した関連付けられたメモのID配列が、作成したエサに紐づいたメモのID配列と、一致しているかを確認（順序非依存）
      $this->assertEqualsCanonicalizing($attachedMemos->pluck('id')->toArray(), $bait->memos->pluck('id')->toArray());

      // エサとユーザーのリレーションが、正しいインスタンスであることを確認
      $this->assertInstanceOf(BelongsTo::class, $bait->user());
      // 自分のユーザーのIDが、作成したエサに紐づいたユーザーのIDと、一致しているかを確認
      $this->assertEquals($this->user->id, $bait->user->id);
   }

   // 自分自身の全てのエサを、取得するスコープのテスト
   public function testAvailableAllBaitsScope()
   {
      // 3件のエサを作成
      $baits = $this->createBaits(3);
      // 全てのエサを取得
      $allBaits = Bait::availableAllBaits()->get();

      // 作成したエサのIDの配列が、取得したエサのIDの配列と、一致するか確認（順序非依存）
      $this->assertEqualsCanonicalizing($baits->pluck('id')->toArray(), $allBaits->pluck('id')->toArray());
   }

   // 自分自身の選択したエサを、取得するスコープのテスト
   public function testAvailableSelectBaitScope()
   {
      // 1件のエサを作成
      $bait = $this->createBaits(1)->first();
      // 選択したエサを取得
      $selectedBait = Bait::availableSelectBait($bait->id)->first();

      // 作成したエサのIDが、取得したエサのIDと、一致しているか確認
      $this->assertEquals($bait->id, $selectedBait->id);
   }

   // 検索キーワードによるエサ名の検索スコープのテスト
   public function testSearchKeywordScope()
   {
      // 既存データをクリア
      Bait::query()->delete();

      // 3件のエサのデータを作成
      Bait::factory()->create(['name' => '大ごい', 'user_id' => $this->user->id]);
      Bait::factory()->create(['name' => 'イモグルテン', 'user_id' => $this->user->id]);
      Bait::factory()->create(['name' => '鯉ごころ', 'user_id' => $this->user->id]);

      // キーワード「鯉」で、エサを検索
      $result = Bait::searchKeyword('鯉')->get();
      $this->assertCount(1, $result);
      // 検索結果の最初の要素のエサに「鯉」が含まれているかを確認
      $this->assertStringContainsString('鯉', $result->first()->name);
   }
}
