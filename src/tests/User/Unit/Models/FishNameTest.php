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

   // 魚名を作成するヘルパーメソッド
   private function createFishNames(int $count): Collection
   {
      // 指定された数の魚名を、現在のユーザーに関連付けて作成する
      return FishName::factory()->count($count)->create(['user_id' => $this->user->id]);
   }

   // 魚名にメモを関連付けるヘルパーメソッド
   private function attachMemos(FishName $fishName, int $memoCount): Collection
   {
      // メモを作成し、魚名に関連付け
      $memos = Memo::factory()->count($memoCount)->create();
      $fishName->memos()->attach($memos->pluck('id')->toArray());

      // リレーションを最新化し（テストの安定化のため）
      $fishName->load('memos');

      // 作成されたメモのコレクションを返す
      return $memos;
   }

   // 基本的なリレーションが、正しく機能しているかのテスト
   public function testFishNameAttributesAndRelations()
   {
      // 1件の魚名を作成
      $fishName = $this->createFishNames(1)->first();
      // 魚名に2件のメモを関連付け
      $attachedMemos = $this->attachMemos($fishName, 2);

      // 魚名とメモのリレーションが、正しいインスタンスであることを確認
      $this->assertInstanceOf(BelongsToMany::class, $fishName->memos());
      // 作成した関連付けられたメモのID配列が、作成した魚名に紐づいたメモのID配列と、一致しているかを確認（順序非依存）
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
      $fishNames = $this->createFishNames(3);
      // 全ての魚名を取得
      $allFishNames = FishName::availableAllFishNames()->get();

      // 作成した魚名のIDの配列が、取得した魚名のIDの配列と、一致しているか確認（順序非依存）
      $this->assertEqualsCanonicalizing($fishNames->pluck('id')->toArray(), $allFishNames->pluck('id')->toArray());
   }

   // 自分自身の選択した魚名を、取得するスコープのテスト
   public function testAvailableSelectFishNameScope()
   {
      // 1件の魚名を作成
      $fishName = $this->createFishNames(1)->first();
      // 選択した魚名を取得
      $selected = FishName::availableSelectFishName($fishName->id)->first();

      // 作成した魚名のIDが、取得した魚名のIDと、一致しているか確認
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
