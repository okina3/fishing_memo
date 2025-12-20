<?php

namespace Tests\User\Unit\Models;

use App\Models\Memo;
use App\Models\Rod;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\User\TestCase;

class RodTest extends TestCase
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

   // ピボット属性付きで釣り竿にメモを関連付けるヘルパーメソッド
   private function attachMemos(Rod $rod, int $count): Collection
   {
      // メモを作成
      $memos = Memo::factory()->count($count)->create(['user_id' => $this->user->id]);
      // ピボット用データを、釣り竿に関連付け
      $pivotData = [];
      foreach ($memos as $memo) {
         $pivotData[$memo->id] = [
            'main_line' => 2.5,
         ];
      }
      $rod->memos()->attach($pivotData);
      // 作成されたメモのコレクションを返す
      return $memos;
   }

   // 基本的なリレーションが、正しく機能しているかのテスト
   public function testRodAttributesAndRelations()
   {
      // 1件の釣り竿を作成
      $rod = Rod::factory()->create(['user_id' => $this->user->id]);
      // 釣り竿に2件のメモを関連付け（ピボットデータ付き）
      $createdMemos = $this->attachMemos($rod, 2);

      // リレーションを最新化しておく（テストの安定化のため）
      $rod->load('memos', 'user');

      // 釣り竿とメモのリレーションが、正しいインスタンスであることを確認
      $this->assertInstanceOf(BelongsToMany::class, $rod->memos());
      // メモのID配列が釣り竿の関連IDと一致するか確認（順序非依存）
      $this->assertEqualsCanonicalizing($createdMemos->pluck('id')->toArray(), $rod->memos->pluck('id')->toArray());

      // 釣り竿とユーザーのリレーションが、正しいインスタンスであることを確認
      $this->assertInstanceOf(BelongsTo::class, $rod->user());
      // 釣り竿のユーザーIDが、作成したユーザーIDと一致することを確認
      $this->assertEquals($this->user->id, $rod->user->id);
   }

   // 自分自身の全ての釣り竿を、取得するスコープのテスト
   public function testAvailableAllRodsScope()
   {
      // 3件の釣り竿を作成
      $rods = Rod::factory()->count(3)->create(['user_id' => $this->user->id]);
      // 全ての釣り竿を取得
      $allRods = Rod::availableAllRods()->get();

      // 釣り竿のID配列が取得結果と一致するか確認（順序非依存）
      $this->assertEqualsCanonicalizing($rods->pluck('id')->toArray(), $allRods->pluck('id')->toArray());
   }

   // 自分自身の選択した釣り竿を、取得するスコープのテスト
   public function testAvailableSelectRodScope()
   {
      // 1件の釣り竿を作成
      $rod = Rod::factory()->create(['user_id' => $this->user->id]);
      // 選択した釣り竿を取得
      $selectedRod = Rod::availableSelectRod($rod->id)->first();

      // 作成した釣り竿IDが取得結果と一致するか確認
      $this->assertEquals($rod->id, $selectedRod->id);
   }

   // 検索キーワードによる釣り竿の検索スコープのテスト
   public function testSearchKeywordScope()
   {
      // 既存データをクリア
      Rod::query()->delete();

      // 3件の釣り竿のデータを作成
      Rod::factory()->create(['name' => 'サンプル竿 10尺', 'user_id' => $this->user->id]);
      Rod::factory()->create(['name' => 'サンプル竿 12尺', 'user_id' => $this->user->id]);
      Rod::factory()->create(['name' => 'サンプル竿 14尺', 'user_id' => $this->user->id]);

      // キーワード「12尺」で検索
      $result = Rod::searchKeyword('12尺')->get();
      $this->assertCount(1, $result);
      // 検索結果の最初の要素の釣り竿に「12尺」が含まれているかを確認
      $this->assertStringContainsString('12尺', $result->first()->name);
   }
}
