<?php

namespace Tests\User\Unit\Models;

use App\Models\Bait;
use App\Models\FishName;
use App\Models\Image;
use App\Models\Memo;
use App\Models\ShareSetting;
use App\Models\Spot;
use App\Models\Tag;
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

    // メモを作成するヘルパーメソッド
    private function createMemos(int $count): Collection
    {
        // 指定された数のメモを、現在のユーザーに関連付けて作成する
        return Memo::factory()->count($count)->create(['user_id' => $this->user->id]);
    }

    // ソフトデリートされたメモを作成するヘルパーメソッド
    private function createDeletedMemos(int $count): Collection
    {
        // 指定された数のメモを、現在のユーザーに関連付けて作成し、deleted_atを設定する
        return Memo::factory()->count($count)->create(['user_id' => $this->user->id, 'deleted_at' => now(),]);
    }

    // メモにエサを関連付けるヘルパーメソッド
    private function attachBaits(Memo $memo, int $baitCount): Collection
    {
        // エサを作成し、メモに関連付け
        $baits = Bait::factory()->count($baitCount)->create();
        $memo->baits()->attach($baits->pluck('id')->toArray());

        // 作成されたエサのコレクションを返す
        return $baits;
    }

    // メモに魚名を関連付けるヘルパーメソッド
    private function attachFishNames(Memo $memo, int $fishNameCount): Collection
    {
        // 魚名を作成
        $fishNames = FishName::factory()->count($fishNameCount)->create();

        // ピボット用データを、メモに関連付け
        $pivotData = [];
        foreach ($fishNames as $fishName) {
            $pivotData[$fishName->id] = ['count' => 1, 'length' => 10];
        }
        $memo->fish_names()->attach($pivotData);

        // 作成された魚名のコレクションを返す
        return $fishNames;
    }

    // メモにタグを関連付けるヘルパーメソッド
    private function attachTags(Memo $memo, int $tagCount): Collection
    {
        // タグを作成し、メモに関連付け
        $tags = Tag::factory()->count($tagCount)->create();
        $memo->tags()->attach($tags->pluck('id')->toArray());

        // 作成されたタグのコレクションを返す
        return $tags;
    }

    // メモに画像を関連付けるヘルパーメソッド
    private function attachImages(Memo $memo, int $imageCount): Collection
    {
        // 画像を作成し、メモに関連付け
        $images = Image::factory()->count($imageCount)->create();
        $memo->images()->attach($images->pluck('id')->toArray());

        // 作成された画像のコレクションを返す
        return $images;
    }

    // 基本的なリレーションが、正しく機能しているかのテスト
    public function testMemoRelations()
    {
        // 1件のメモを作成
        $memo = $this->createMemos(1)->first();
        // スポットを作成し、メモに関連付け
        $spot = Spot::factory()->create(['user_id' => $this->user->id]);
        $memo->spot()->associate($spot);
        $memo->save();
        // メモに2件のタグを関連付け
        $attachedTags = $this->attachTags($memo, 2);
        // メモに2件の画像を関連付け
        $attachedImages = $this->attachImages($memo, 2);
        // メモに2件のエサを関連付け
        $attachedBaits = $this->attachBaits($memo, 2);
        // メモに2件の魚名を関連付け
        $attachedFishNames = $this->attachFishNames($memo, 2);
        // リレーションを最新化しておく（テストの安定化のため）
        $memo->load(['tags', 'images', 'baits', 'fish_names', 'spot']);

        // メモと釣り場のリレーションが、正しいインスタンスであることを確認
        $this->assertInstanceOf(BelongsTo::class, $memo->spot());
        // 作成した釣り場のIDが、メモに紐づいた釣り場のIDと、一致しているかを確認
        $this->assertEquals($spot->id, $memo->spot->id);

        // メモとエサのリレーションが、正しいインスタンスであることを確認
        $this->assertInstanceOf(BelongsToMany::class, $memo->baits());
        // 作成した関連付けられたエサのID配列が、作成したメモに紐づいたエサのID配列と、一致しているかを確認（順序非依存）
        $this->assertEqualsCanonicalizing($attachedBaits->pluck('id')->toArray(), $memo->baits->pluck('id')->toArray());

        // メモと魚名のリレーションが、正しいインスタンスであることを確認
        $this->assertInstanceOf(BelongsToMany::class, $memo->fish_names());
        // 作成した関連付けられた魚名のID配列が、作成したメモに紐づいた魚名のID配列と、一致しているかを確認（順序非依存）
        $this->assertEqualsCanonicalizing($attachedFishNames->pluck('id')->toArray(), $memo->fish_names->pluck('id')->toArray());

        // メモとタグのリレーションが、正しいインスタンスであることを確認
        $this->assertInstanceOf(BelongsToMany::class, $memo->tags());
        // 作成した関連付けられたタグのID配列が、作成したメモに紐づいたタグのID配列と、一致しているかを確認（順序非依存）
        $this->assertEqualsCanonicalizing($attachedTags->pluck('id')->toArray(), $memo->tags->pluck('id')->toArray());

        // メモと画像のリレーションが、正しいインスタンスであることを確認
        $this->assertInstanceOf(BelongsToMany::class, $memo->images());
        // 作成した関連付けられた画像のID配列が、作成したメモに紐づいた画像のID配列と、一致しているかを確認（順序非依存）
        $this->assertEqualsCanonicalizing($attachedImages->pluck('id')->toArray(), $memo->images->pluck('id')->toArray());

        // 共有設定を作成し、メモに関連付け
        $shareSetting = ShareSetting::factory()->create(['memo_id' => $memo->id]);

        // メモと共有設定のリレーションが、正しいインスタンスであることを確認
        $this->assertInstanceOf(HasMany::class, $memo->shareSettings());
        // 作成した共有設定のIDの配列が、メモに紐づいた共有設定のIDの配列と、一致しているかを確認（単一作成のため配列化して比較）
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
        $memos = $this->createMemos(3);
        // 全てのメモを取得
        $allMemos = Memo::availableAllMemos()->get();

        // 作成したメモのIDの配列が、取得したメモのIDの配列と、一致するか確認（順序非依存）
        $this->assertEqualsCanonicalizing($memos->pluck('id')->toArray(), $allMemos->pluck('id')->toArray());
    }

    // 自分自身の選択したメモを、取得するスコープのテスト
    public function testAvailableSelectMemoScope()
    {
        // 1件のメモを作成
        $memo = $this->createMemos(1)->first();
        // 選択したメモを取得
        $selectedMemo = Memo::availableSelectMemo($memo->id)->first();

        // 作成したメモのIDが、取得したメモのIDと、一致するか確認
        $this->assertEquals($memo->id, $selectedMemo->id);
    }

    // 自分自身の全ての削除済みのメモを、取得するスコープのテスト
    public function testAvailableAllTrashedMemosScope()
    {
        // 3件のソフトデリートしたメモを作成
        $memos = $this->createDeletedMemos(3);
        // 全てのソフトデリートしたメモを取得
        $trashedMemos = Memo::availableAllTrashedMemos()->get();

        // 作成した削除済みメモのIDの配列が、取得した削除済みメモのIDの配列と、一致するか確認（順序非依存）
        $this->assertEqualsCanonicalizing($memos->pluck('id')->toArray(), $trashedMemos->pluck('id')->toArray());
    }

    // 自分自身の選択した削除済みのメモを、取得するスコープのテスト
    public function testAvailableSelectTrashedMemoScope()
    {
        // 1件のソフトデリートしたメモを作成
        $memo = $this->createDeletedMemos(1)->first();
        // 選択した削除済みのメモを取得
        $selectedTrashedMemo = Memo::availableSelectTrashedMemo($memo->id)->first();

        // 作成した削除済みメモのIDが、取得した削除済みメモのIDと、一致するか確認
        $this->assertEquals($memo->id, $selectedTrashedMemo->id);
    }
}
