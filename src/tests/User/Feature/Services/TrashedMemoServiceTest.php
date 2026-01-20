<?php

namespace Tests\User\Feature\Services;

use App\Models\Bait;
use App\Models\FishName;
use App\Models\Image;
use App\Models\Memo;
use App\Models\Spot;
use App\Models\User;
use App\Services\User\TrashedMemoService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\User\TestCase;

class TrashedMemoServiceTest extends TestCase
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

   // 選択したメモの中間テーブルを削除するメソッドのテスト
   public function testDeleteRelatedRecords()
   {
      // メモ、釣り場、エサ、魚名、画像、を作成
      $memo = Memo::factory()->create(['user_id' => $this->user->id]);
      $spot = Spot::factory()->create(['user_id' => $this->user->id]);
      $bait = Bait::factory()->create(['user_id' => $this->user->id]);
      $fishName = FishName::factory()->create(['user_id' => $this->user->id]);
      $image = Image::factory()->create(['user_id' => $this->user->id]);

      // メモと各モデルを中間テーブルに関連付け
      $memo->spots()->attach($spot->id, [
         'river_flow' => 'あり',
         'turbidity' => '濁りなし',
         'water_level' => 1.5,
         'water_temp' => 20,
      ]);
      $memo->baits()->attach($bait->id);
      $memo->fish_names()->attach($fishName->id, [
         'count' => 2,
         'length' => 25
      ]);
      $memo->images()->attach($image->id);

      // メモに紐づいた中間テーブルのレコードを削除するサービスメソッドを実行
      TrashedMemoService::deleteRelatedRecords($memo->id);

      // メモに紐づいた中間テーブルのレコードが削除されていることを確認
      $this->assertDatabaseMissing('memo_spots', [
         'memo_id' => $memo->id,
         'spot_id' => $spot->id
      ]);
      $this->assertDatabaseMissing('memo_baits', [
         'memo_id' => $memo->id,
         'bait_id' => $bait->id
      ]);
      $this->assertDatabaseMissing('memo_fish_names', [
         'memo_id' => $memo->id,
         'fish_name_id' => $fishName->id
      ]);
      $this->assertDatabaseMissing('memo_images', [
         'memo_id' => $memo->id,
         'image_id' => $image->id
      ]);
   }
}
