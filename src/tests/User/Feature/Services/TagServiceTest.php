<?php

namespace Tests\User\Feature\Services;

use App\Models\Memo;
use App\Models\Tag;
use App\Models\User;
use App\Services\TagService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\User\TestCase;

class TagServiceTest extends TestCase
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

   // メモを作成するヘルパーメソッド
   private function createMemos(int $count): Collection
   {
      // 指定された数のメモを、現在のユーザーに関連付けて作成する
      return Memo::factory()->count($count)->create(['user_id' => $this->user->id]);
   }

   // メモにタグを関連付けるヘルパーメソッド
   private function attachTags(Memo $memo, int $tagCount): Collection
   {
      // タグを作成し、メモに関連付け
      $tags = Tag::factory()->count($tagCount)->create(['user_id' => $this->user->id]);
      $memo->tags()->attach($tags->pluck('id')->toArray());

      // 作成したタグのコレクションを返す
      return $tags;
   }

   // タグの保存のテスト
   public function testCreateTag()
   {
      // タグ名を作成
      $tagName = 'テストタグ';
      // タグを保存
      $tag = TagService::createTag($tagName);

      // 作成されたタグがDBに存在するかを確認
      $this->assertDatabaseHas('tags', [
         'id' => $tag->id,
         'name' => $tagName,
         'user_id' => $this->user->id,
      ]);
   }

   // 新規タグの保存と更新のテスト
   public function testCreateNewTag()
   {
      // 1件の自分のメモを作成
      $memo = $this->createMemos(1)->first();
      // 新規タグの入力
      $newTagName = '新しいタグ';
      // 新規タグを保存するサービスメソッドを実行
      TagService::createNewTag($newTagName, $memo->id);

      // タグが保存されていることを確認
      $this->assertDatabaseHas('tags', ['name' => $newTagName, 'user_id' => $this->user->id,]);
   }

   // メモに紐づいたタグのIDを取得するのテスト
   public function testGetMemoTagsId()
   {
      // 1件の自分のメモを作成
      $memo = $this->createMemos(1)->first();
      // メモに2件のタグを関連付け
      $attachedTags = $this->attachTags($memo, 2);
      // メモに紐づいたタグのIDを取得するサービスメソッドを実行
      $memoTagsById = TagService::getMemoTagsId($attachedTags);

      // 作成した関連付けられたタグのID配列が、取得したメモに紐づいたタグのID配列と、一致しているかを確認
      $this->assertEquals($attachedTags->pluck('id')->toArray(), $memoTagsById);
   }

   // メモに紐づいたタグのNameを取得するのテスト
   public function testGetMemoTagsName()
   {
      // 1件の自分のメモを作成
      $memo = $this->createMemos(1)->first();
      // メモに2件のタグを関連付け
      $attachedTags = $this->attachTags($memo, 2);
      // メモに紐づいたタグの名前を取得するサービスメソッドを実行
      $memoTagsByName = TagService::getMemoTagsName($attachedTags);

      // 作成した関連付けられたタグのName配列が、取得したメモに紐づいたタグのName配列と、一致しているかを確認
      $this->assertEquals($attachedTags->pluck('name')->toArray(), $memoTagsByName);
   }

   // タグの一括削除のテスト
   public function testDeleteTags()
   {
      // 3件のタグを作成
      $tags = Tag::factory()->count(3)->create(['user_id' => $this->user->id]);
      // 削除するタグのID配列を作成
      $tagIdsToDelete = $tags->pluck('id')->toArray();
      // タグを一括削除するサービスメソッドを実行
      TagService::deleteTags($tagIdsToDelete);

      // 各タグがDBに存在しないことを確認
      foreach ($tagIdsToDelete as $tagId) {
         $this->assertDatabaseMissing('tags', ['id' => $tagId, 'user_id' => $this->user->id,]);
      }
   }
}
