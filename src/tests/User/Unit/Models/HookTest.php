<?php

namespace Tests\User\Unit\Models;

use App\Models\Hook;
use App\Models\Memo;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\User\TestCase;

class HookTest extends TestCase
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

   // ピボット属性付きで釣り針にメモを関連付けるヘルパーメソッド
   private function attachMemos(Hook $hook, int $count): Collection
   {
      // メモを作成
      $memos = Memo::factory()->count($count)->create(['user_id' => $this->user->id]);
      // ピボット用データを、釣り針に関連付け
      $pivotData = [];
      foreach ($memos as $memo) {
         $pivotData[$memo->id] = [
            'leader_size' => 1.2,
            'leader_upper_cm' => 30,
            'leader_lower_cm' => 20,
         ];
      }
      $hook->memos()->attach($pivotData);
      // 作成されたメモのコレクションを返す
      return $memos;
   }

   // 基本的なリレーションが、正しく機能しているかのテスト
   public function testHookAttributesAndRelations()
   {
      // 1件の釣り針を作成
      $hook = Hook::factory()->create(['user_id' => $this->user->id]);
      // 釣り針に2件のメモを関連付け（ピボットデータ付き）
      $createdMemos = $this->attachMemos($hook, 2);

      // リレーションを最新化しておく（テストの安定化のため）
      $hook->load('memos', 'user');

      // 釣り針とメモのリレーションが、正しいインスタンスであることを確認
      $this->assertInstanceOf(BelongsToMany::class, $hook->memos());
      // メモのID配列が釣り針の関連IDと一致するか確認（順序非依存）
      $this->assertEqualsCanonicalizing($createdMemos->pluck('id')->toArray(), $hook->memos->pluck('id')->toArray());

      // 釣り針とユーザーのリレーションが、正しいインスタンスであることを確認
      $this->assertInstanceOf(BelongsTo::class, $hook->user());
      // 自分のユーザーのIDが、作成した釣り針に紐づいたユーザーのIDと、一致しているかを確認
      $this->assertEquals($this->user->id, $hook->user->id);
   }

   // 自分自身の全ての釣り針を、取得するスコープのテスト
   public function testAvailableAllHooksScope()
   {
      // 3件の釣り針を作成
      $hooks = Hook::factory()->count(3)->create(['user_id' => $this->user->id]);
      // 全ての釣り針を取得
      $all = Hook::availableAllHooks()->get();

      // 釣り針のID配列が取得結果と一致するか確認（順序非依存）
      $this->assertEqualsCanonicalizing($hooks->pluck('id')->toArray(), $all->pluck('id')->toArray());
   }

   // 自分自身の選択した釣り針を、取得するスコープのテスト
   public function testAvailableSelectHookScope()
   {
      // 1件の釣り針を作成
      $hook = Hook::factory()->create(['user_id' => $this->user->id]);
      // 選択した釣り針を取得
      $selected = Hook::availableSelectHook($hook->id)->first();

      // 作成した釣り針IDが取得結果と一致するか確認
      $this->assertEquals($hook->id, $selected->id);
   }

   // 検索キーワードによる釣り針の検索スコープのテスト
   public function testSearchKeywordScope()
   {
      // 既存データをクリア
      Hook::query()->delete();

      // 3件の釣り針のデータを作成
      Hook::factory()->create(['name' => '伊勢尼 13号', 'user_id' => $this->user->id]);
      Hook::factory()->create(['name' => 'プロスト 10号', 'user_id' => $this->user->id]);
      Hook::factory()->create(['name' => '伊勢尼 10号', 'user_id' => $this->user->id]);

      // キーワード「伊勢尼」で検索
      $result = Hook::searchKeyword('伊勢尼')->get();
      $this->assertCount(2, $result);
      // 検索結果の最初の要素の釣り針に「伊勢尼」が含まれているかを確認
      $this->assertStringContainsString('伊勢尼', $result->first()->name);
   }
}
