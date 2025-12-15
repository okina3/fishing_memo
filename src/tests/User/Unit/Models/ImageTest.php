<?php

namespace Tests\User\Unit\Models;

use App\Models\Image;
use App\Models\Memo;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\User\TestCase;

class ImageTest extends TestCase
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
   public function testImageAttributesAndRelations()
   {
      // 1件の画像を作成
      $image = Image::factory()->create(['user_id' => $this->user->id]);
      // メモを作成し、2検の画像に関連付け
      $attachedMemos = Memo::factory()->count(2)->create();
      $image->memos()->attach($attachedMemos->pluck('id')->toArray());

      // リレーションを最新化しておく（テストの安定化のため）
      $image->load('memos');

      // 画像とメモのリレーションが、正しいインスタンスであることを確認
      $this->assertInstanceOf(BelongsToMany::class, $image->memos());
      // メモのID配列が画像の関連IDと一致するか確認（順序非依存）
      $this->assertEqualsCanonicalizing($attachedMemos->pluck('id')->toArray(), $image->memos->pluck('id')->toArray());

      // 画像とユーザーのリレーションが、正しいインスタンスであることを確認
      $this->assertInstanceOf(BelongsTo::class, $image->user());
      // 自分のユーザーのIDが、作成した画像に紐づいたユーザーのIDと、一致しているかを確認
      $this->assertEquals($this->user->id, $image->user->id);
   }

   // 自分自身の全ての画像のデータを、取得するスコープのテスト
   public function testAvailableAllImagesScope()
   {
      // 3件の画像を作成
      $images = Image::factory()->count(3)->create(['user_id' => $this->user->id]);
      // 全ての画像を取得
      $allImages = Image::availableAllImages()->get();

      // 画像のID配列が取得結果と一致するか確認（順序非依存）
      $this->assertEqualsCanonicalizing($images->pluck('id')->toArray(), $allImages->pluck('id')->toArray());
   }

   // 自分自身の選択した画像のデータを、取得するスコープのテスト
   public function testAvailableSelectImageScope()
   {
      // 1件の画像を作成
      $image = Image::factory()->create(['user_id' => $this->user->id]);
      // 選択した画像を取得
      $selectedImage = Image::availableSelectImage($image->id)->first();

      // 作成した画像IDが取得結果と一致するか確認
      $this->assertEquals($image->id, $selectedImage->id);
   }
}
