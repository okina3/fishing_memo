<?php

namespace Tests\User\Unit\Models;

use App\Models\Memo;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\User\TestCase;

class TagTest extends TestCase
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

    // タグを作成するヘルパーメソッド
    private function createTags(int $count): Collection
    {
        // 指定された数のタグを、現在のユーザーに関連付けて作成する
        return Tag::factory()->count($count)->create(['user_id' => $this->user->id]);
    }

    // タグにメモを関連付けるヘルパーメソッド
    private function attachMemos(Tag $tag, int $memoCount): Collection
    {
        // メモを作成し、タグに関連付け
        $memos = Memo::factory()->count($memoCount)->create();
        $tag->memos()->attach($memos->pluck('id')->toArray());

        // リレーションを最新化し（テストの安定化のため）
        $tag->load('memos');

        // 作成されたメモのコレクションを返す
        return $memos;
    }

    // 基本的なリレーションが、正しく機能しているかのテスト
    public function testTagAttributesAndRelations()
    {
        // 1件のタグを作成
        $tag = $this->createTags(1)->first();
        // タグに2件のメモを関連付け
        $attachedMemos = $this->attachMemos($tag, 2);

        // タグとメモのリレーションが、正しいインスタンスであることを確認
        $this->assertInstanceOf(BelongsToMany::class, $tag->memos());
        // 作成した関連付けられたメモのID配列が、作成したタグに紐づいたメモのID配列と、一致しているかを確認（順序非依存）
        $this->assertEqualsCanonicalizing($attachedMemos->pluck('id')->toArray(), $tag->memos->pluck('id')->toArray());

        // タグとユーザーのリレーションが、正しいインスタンスであることを確認
        $this->assertInstanceOf(BelongsTo::class, $tag->user());
        // 自分のユーザーのIDが、作成したタグに紐づいたユーザーのIDと、一致しているかを確認
        $this->assertEquals($this->user->id, $tag->user->id);
    }

    // 自分自身の全てのタグを、取得するスコープのテスト
    public function testAvailableAllTagsScope()
    {
        // 3件のタグを作成
        $tags = $this->createTags(3);
        // 全てのタグを取得
        $allTags = Tag::availableAllTags()->get();

        // 作成したタグのIDの配列が、取得したタグのIDの配列と、一致するか確認（順序非依存）
        $this->assertEqualsCanonicalizing($tags->pluck('id')->toArray(), $allTags->pluck('id')->toArray());
    }

    // 自分自身の選択したタグを、取得するスコープのテスト
    public function testAvailableSelectTagScope()
    {
        // 1件のタグを作成
        $tag = $this->createTags(1)->first();
        // 選択したタグを取得
        $selectedTag = Tag::availableSelectTag($tag->id)->first();

        // 作成したタグのIDが、取得したタグのIDと、一致するか確認
        $this->assertEquals($tag->id, $selectedTag->id);
    }
}
