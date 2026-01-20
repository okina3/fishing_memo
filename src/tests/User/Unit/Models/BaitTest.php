<?php

namespace Tests\User\Unit\Models;

use App\Models\Bait;
use App\Models\Memo;
use App\Models\User;
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

   // 基本的なリレーションが、正しく機能しているかのテスト
   public function testBaitAttributesAndRelations()
   {
      // 1件のエサを作成
      $bait = Bait::factory()->create(['user_id' => $this->user->id]);
      // メモを作成し、2件のエサに関連付け
      $attachedMemos = Memo::factory()->count(2)->create();
      $bait->memos()->attach($attachedMemos->pluck('id')->toArray());

      // リレーションを最新化しておく（テストの安定化のため）
      $bait->load('memos');

      // エサとメモのリレーションが、正しいインスタンスであることを確認
      $this->assertInstanceOf(BelongsToMany::class, $bait->memos());
      // メモのID配列がエサの関連IDと一致するか確認（順序非依存）
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
      $baits = Bait::factory()->count(3)->create(['user_id' => $this->user->id]);
      // 全てのエサを取得
      $allBaits = Bait::availableAllBaits()->get();

      // エサのID配列が取得結果と一致するか確認（順序非依存）
      $this->assertEqualsCanonicalizing($baits->pluck('id')->toArray(), $allBaits->pluck('id')->toArray());
   }

   // 自分自身の選択したエサを、取得するスコープのテスト
   public function testAvailableSelectBaitScope()
   {
      // 1件のエサを作成
      $bait = Bait::factory()->create(['user_id' => $this->user->id]);
      // 選択したエサを取得
      $selectedBait = Bait::availableSelectBait($bait->id)->first();

      // 作成したエサIDが取得結果と一致するか確認
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
