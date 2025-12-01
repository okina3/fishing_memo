<?php

namespace Tests\User\Unit\Models;

use App\Models\Image;
use App\Models\Memo;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
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
        // 認証済みのユーザーを返す（users ガードを明示）
        $this->actingAs($this->user, 'users');
    }

    // 画像を作成するヘルパーメソッド
    private function createImages(int $count): Collection
    {
        // 指定された数の画像を、現在のユーザーに関連付けて作成する
        return Image::factory()->count($count)->create(['user_id' => $this->user->id]);
    }

    // 画像にメモを関連付けるヘルパーメソッド
    private function attachMemos(Image $image, int $memoCount): Collection
    {
        // メモを作成し、画像に関連付け
        $memos = Memo::factory()->count($memoCount)->create();
        $image->memos()->attach($memos->pluck('id')->toArray());

        // リレーションを最新化し（テストの安定化のため）
        $image->load('memos');

        // 作成されたメモのコレクションを返す
        return $memos;
    }

    // 基本的なリレーションが、正しく機能しているかのテスト
    public function testImageAttributesAndRelations()
    {
        // 1件の画像を作成
        $image = $this->createImages(1)->first();
        // 画像に2件のメモを関連付け
        $attachedMemos = $this->attachMemos($image, 2);

        // 画像とメモのリレーションが、正しいインスタンスであることを確認
        $this->assertInstanceOf(BelongsToMany::class, $image->memos());
        // 作成した関連付けられたメモのID配列が、作成した画像に紐づいたメモのID配列と、一致しているかを確認（順序非依存）
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
        $images = $this->createImages(3);
        // 全ての画像を取得
        $allImages = Image::availableAllImages()->get();

        // 作成した画像のIDの配列が、取得した画像のIDの配列と、一致するか確認（順序非依存）
        $this->assertEqualsCanonicalizing($images->pluck('id')->toArray(), $allImages->pluck('id')->toArray());
    }

    // 自分自身の選択した画像のデータを、取得するスコープのテスト
    public function testAvailableSelectImageScope()
    {
        // 1件の画像を作成
        $image = $this->createImages(1)->first();
        // 選択した画像を取得
        $selectedImage = Image::availableSelectImage($image->id)->first();

        // 作成した画像のIDが、取得した画像のIDと、一致するか確認
        $this->assertEquals($image->id, $selectedImage->id);
    }
}
