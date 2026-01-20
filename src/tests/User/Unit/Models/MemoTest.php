<?php

namespace Tests\User\Unit\Models;

use App\Models\Bait;
use App\Models\FishName;
use App\Models\Image;
use App\Models\Memo;
use App\Models\ShareSetting;
use App\Models\Spot;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\User\TestCase;

class MemoTest extends TestCase
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

   // ピボット属性付きでメモに魚名を関連付けるヘルパーメソッド
   private function attachFishNames(Memo $memo, int $fishNameCount): Collection
   {
      // 魚名を作成
      $fishNames = FishName::factory()->count($fishNameCount)->create();
      // ピボット用データを、メモに関連付け
      $pivotData = [];
      foreach ($fishNames as $fishName) {
         $pivotData[$fishName->id] = [
            'count' => 1,
            'length' => 10
         ];
      }
      $memo->fish_names()->attach($pivotData);
      // 作成された魚名のコレクションを返す
      return $fishNames;
   }

   // ピボット属性付きでメモに釣り場を関連付けるヘルパーメソッド
   private function attachSpots(Memo $memo, int $spotCount): Collection
   {
      // 釣り場を作成
      $spots = Spot::factory()->count($spotCount)->create(['user_id' => $this->user->id]);
      // ピボット用データを、メモに関連付け
      $pivotData = [];
      foreach ($spots as $spot) {
         $pivotData[$spot->id] = [
            'river_flow' => 'あり',
            'turbidity' => '濁り',
            'water_level' => 1.2,
            'water_temp' => 15,
         ];
      }
      $memo->spots()->attach($pivotData);
      // 作成された釣り場のコレクションを返す
      return $spots;
   }

   // 基本的なリレーションが、正しく機能しているかのテスト
   public function testMemoRelations()
   {
      // 1件のメモを作成
      $memo = Memo::factory()->create(['user_id' => $this->user->id]);
      // メモに2件の釣り場を関連付け（ピボットデータ付き）
      $attachedSpots = $this->attachSpots($memo, 2);
      // メモに2件のエサを関連付け
      $attachedBaits = Bait::factory()->count(2)->create();
      $memo->baits()->attach($attachedBaits->pluck('id')->toArray());
      // メモに2件の魚名を関連付け（ピボットデータ付き）
      $attachedFishNames = $this->attachFishNames($memo, 2);
      // メモに2件の画像を関連付け
      $attachedImages = Image::factory()->count(2)->create();
      $memo->images()->attach($attachedImages->pluck('id')->toArray());

      // リレーションを最新化しておく（テストの安定化のため）
      $memo->load(['images', 'baits', 'fish_names', 'spots']);

      // メモと釣り場のリレーションが、正しいインスタンスであることを確認
      $this->assertInstanceOf(BelongsToMany::class, $memo->spots());
      // 釣り場のID配列がメモの関連IDと一致するか確認（順序非依存）
      $this->assertEqualsCanonicalizing($attachedSpots->pluck('id')->toArray(), $memo->spots->pluck('id')->toArray());

      // メモとエサのリレーションが、正しいインスタンスであることを確認
      $this->assertInstanceOf(BelongsToMany::class, $memo->baits());
      // エサのID配列がメモの関連IDと一致するか確認（順序非依存）
      $this->assertEqualsCanonicalizing($attachedBaits->pluck('id')->toArray(), $memo->baits->pluck('id')->toArray());

      // メモと魚名のリレーションが、正しいインスタンスであることを確認
      $this->assertInstanceOf(BelongsToMany::class, $memo->fish_names());
      // 魚名のID配列がメモの関連IDと一致するか確認（順序非依存）
      $this->assertEqualsCanonicalizing($attachedFishNames->pluck('id')->toArray(), $memo->fish_names->pluck('id')->toArray());

      // メモと画像のリレーションが、正しいインスタンスであることを確認
      $this->assertInstanceOf(BelongsToMany::class, $memo->images());
      // 画像のID配列がメモの関連IDと一致するか確認（順序非依存）
      $this->assertEqualsCanonicalizing($attachedImages->pluck('id')->toArray(), $memo->images->pluck('id')->toArray());

      // 共有設定を作成し、メモに関連付け
      $shareSetting = ShareSetting::factory()->create(['memo_id' => $memo->id]);

      // メモと共有設定のリレーションが、正しいインスタンスであることを確認
      $this->assertInstanceOf(HasMany::class, $memo->shareSettings());
      // 共有設定のID配列がメモの関連IDと一致するか確認
      $this->assertEqualsCanonicalizing([$shareSetting->id], $memo->shareSettings->pluck('id')->toArray());

      // メモとユーザーのリレーションが、正しいインスタンスであることを確認
      $this->assertInstanceOf(BelongsTo::class, $memo->user());
      // 自分のユーザーのIDが、作成したメモに紐づいたユーザーのIDと、一致しているかを確認
      $this->assertEquals($this->user->id, $memo->user->id);
   }

   // 自分自身の全てのメモを、取得するスコープのテスト
   public function testAvailableAllMemosScope()
   {
      // 3件のメモを作成
      $memos = Memo::factory()->count(3)->create(['user_id' => $this->user->id]);
      // 全てのメモを取得
      $allMemos = Memo::availableAllMemos()->get();

      // メモのID配列が取得結果と一致するか確認（順序非依存）
      $this->assertEqualsCanonicalizing($memos->pluck('id')->toArray(), $allMemos->pluck('id')->toArray());
   }

   // 自分自身の選択したメモを、取得するスコープのテスト
   public function testAvailableSelectMemoScope()
   {
      // 1件のメモを作成
      $memo = Memo::factory()->create(['user_id' => $this->user->id]);
      // 選択したメモを取得
      $selectedMemo = Memo::availableSelectMemo($memo->id)->first();

      // 作成したメモIDが取得結果と一致するか確認
      $this->assertEquals($memo->id, $selectedMemo->id);
   }

   // 自分自身の全ての削除済みのメモを、取得するスコープのテスト
   public function testAvailableAllTrashedMemosScope()
   {
      // 3件のソフトデリートしたメモを作成
      $memos = Memo::factory()->count(3)->create(['user_id' => $this->user->id, 'deleted_at' => now()]);
      // 全てのソフトデリートしたメモを取得
      $trashedMemos = Memo::availableAllTrashedMemos()->get();

      // 削除済みメモID配列が取得結果と一致するか確認（順序非依存）
      $this->assertEqualsCanonicalizing($memos->pluck('id')->toArray(), $trashedMemos->pluck('id')->toArray());
   }

   // 自分自身の選択した削除済みのメモを、取得するスコープのテスト
   public function testAvailableSelectTrashedMemoScope()
   {
      // 1件のソフトデリートしたメモを作成
      $memo = Memo::factory()->count(1)->create(['user_id' => $this->user->id, 'deleted_at' => now()])->first();
      // 選択した削除済みのメモを取得
      $selectedTrashedMemo = Memo::availableSelectTrashedMemo($memo->id)->first();

      // 削除済みメモIDが取得結果と一致するか確認
      $this->assertEquals($memo->id, $selectedTrashedMemo->id);
   }

   // メモを、検索するスコープのテスト
   public function testSearchKeywordScope()
   {
      // 既存データをクリア
      Memo::query()->delete();

      // 3件のメモのデータを作成
      Memo::factory()->create([
         'fishing_date' => '2022-12-01',
         'content' => 'よく釣れた。',
         'user_id' => $this->user->id
      ]);
      Memo::factory()->create([
         'fishing_date' => '2023-8-12',
         'content' => '風が強くて大変だった。',
         'user_id' => $this->user->id
      ]);
      Memo::factory()->create([
         'fishing_date' => '2025-6-17',
         'content' => '快適に釣りができた。',
         'user_id' => $this->user->id
      ]);

      // キーワード「風」で、メモを検索
      $searchResults = Memo::searchKeyword('風')->get();

      // 検索結果が1件であることを確認
      $this->assertCount(1, $searchResults);
      // 検索結果の最初の要素の content に「風」が含まれているかを確認
      $this->assertStringContainsString('風', $searchResults->first()->content);
   }
}
