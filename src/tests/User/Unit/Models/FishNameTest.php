<?php

namespace Tests\User\Unit\Models;

use App\Models\FishName;
use App\Models\Memo;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\User\TestCase;

class FishNameTest extends TestCase
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

   // ピボット属性付きで魚名にメモを関連付けるヘルパーメソッド
   private function attachMemos(FishName $fishName, int $memoCount): Collection
   {
      // メモを作成
      $memos = Memo::factory()->count($memoCount)->create(['user_id' => $this->user->id]);
      // ピボット用データを、魚名に関連付け
      $pivotData = [];
      foreach ($memos as $memo) {
         $pivotData[$memo->id] = [
            'count' => 1,
            'length' => 10,
         ];
      }
      $fishName->memos()->attach($pivotData);
      // 作成されたメモのコレクションを返す
      return $memos;
   }

   // 基本的なリレーションが、正しく機能しているかのテスト
   public function testFishNameAttributesAndRelations()
   {
      // 1件の魚名を作成
      $fishName = FishName::factory()->create(['user_id' => $this->user->id]);
      // 魚名に2件のメモを関連付け（ピボットデータ付き）
      $attachedMemos = $this->attachMemos($fishName, 2);

      // リレーションを最新化しておく（テストの安定化のため）
      $fishName->load('memos');

      // 魚名とメモのリレーションが、正しいインスタンスであることを確認
      $this->assertInstanceOf(BelongsToMany::class, $fishName->memos());
      // メモのID配列が魚名の関連IDと一致するか確認（順序非依存）
      $this->assertEqualsCanonicalizing($attachedMemos->pluck('id')->toArray(), $fishName->memos->pluck('id')->toArray());

      // 魚名とユーザーのリレーションが、正しいインスタンスであることを確認
      $this->assertInstanceOf(BelongsTo::class, $fishName->user());
      // 自分のユーザーのIDが、作成した魚名に紐づいたユーザーのIDと、一致しているかを確認
      $this->assertEquals($this->user->id, $fishName->user->id);
   }

   // 自分自身の全ての魚名を、取得するスコープのテスト
   public function testAvailableAllFishNamesScope()
   {
      // 3件の魚名を作成
      $fishNames = FishName::factory()->count(3)->create(['user_id' => $this->user->id]);
      // 全ての魚名を取得
      $allFishNames = FishName::availableAllFishNames()->get();

      // 魚名のID配列が取得結果と一致するか確認（順序非依存）
      $this->assertEqualsCanonicalizing($fishNames->pluck('id')->toArray(), $allFishNames->pluck('id')->toArray());
   }

   // 自分自身の選択した魚名を、取得するスコープのテスト
   public function testAvailableSelectFishNameScope()
   {
      // 1件の魚名を作成
      $fishName = FishName::factory()->create(['user_id' => $this->user->id]);
      // 選択した魚名を取得
      $selected = FishName::availableSelectFishName($fishName->id)->first();

      // 作成した魚名IDが取得結果と一致するか確認
      $this->assertEquals($fishName->id, $selected->id);
   }

   // 検索キーワードによる魚名の検索スコープのテスト
   public function testSearchKeywordScope()
   {
      // 既存データをクリア
      FishName::query()->delete();

      // 3件の魚名のデータを作成
      FishName::factory()->create(['name' => 'コイ', 'user_id' => $this->user->id]);
      FishName::factory()->create(['name' => 'ヘラブナ', 'user_id' => $this->user->id]);
      FishName::factory()->create(['name' => 'ブラックバス', 'user_id' => $this->user->id]);

      // キーワード「コイ」で、魚名を検索
      $result = FishName::searchKeyword('コイ')->get();
      $this->assertCount(1, $result);
      // 検索結果の最初の要素の魚名に「コイ」が含まれているかを確認
      $this->assertStringContainsString('コイ', $result->first()->name);
   }
}
