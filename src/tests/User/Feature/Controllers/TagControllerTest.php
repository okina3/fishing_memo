<?php

namespace Tests\User\Feature\Controllers;

use App\Models\Tag;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\User\TestCase;

class TagControllerTest extends TestCase
{
    use RefreshDatabase;
    use MockeryPHPUnitIntegration;

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

    // タグの一覧、タグの新規作成画面が、正しく表示されることをテスト
    public function testIndexTagController()
    {
        // 認証済みユーザーでタグ一覧ルートへアクセス
        $response = $this->get(route('user.tag.index'));

        // ステータスコード200（OK）であることを検証
        $response->assertOk();
        // 返却されるビューが期待通り（user.tags.index）であることを検証
        $response->assertViewIs('user.tags.index');
        // ビューに渡される主要なデータ（全タグ）が存在することを検証
        $response->assertViewHasAll(['all_tags']);
    }

    // タグが、正しく保存されることをテスト
    public function testStoreTagController()
    {
        // リクエストデータを作成
        $requestData = ['new_tag' => 'テスト、新規タグ'];

        // タグを保存するの為に、リクエストを送信
        $response = $this->post(route('user.tag.store'), $requestData);

        // リダイレクトで成功メッセージがフラッシュされていることを検証
        $response->assertRedirect(route('user.tag.index'));
        $response->assertSessionHas(['message' => 'タグを登録しました。', 'status' => 'success']);

        // タグが作成されていることを検証
        $this->assertDatabaseHas('tags', [
            'user_id' => $this->user->id,
            'name' => 'テスト、新規タグ',
        ]);
    }

    // タグが、正しく保存される時のエラーハンドリングをテスト
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function testErrorStoreTagController()
    {
        // リクエストデータを作成
        $requestData = ['new_tag' => '例外テストタグ'];

        // TagService::createTag が例外を投げるようにエイリアスモック
        $tagServiceMock = Mockery::mock('alias:App\\Services\\TagService');
        $tagServiceMock->shouldReceive('createTag')
            ->once()->andThrow(new Exception('forced error'));

        // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
        Log::shouldReceive('error')->once()->withAnyArgs();

        // タグ保存のリクエスト送信
        $response = $this->from(route('user.tag.index'))->post(route('user.tag.store'), $requestData);

        // リダイレクトでエラーがフラッシュされていることを検証
        $response->assertRedirect(route('user.tag.index'));
        $response->assertSessionHas(['message' => 'タグの登録に失敗しました。', 'status' => 'error']);

        // レコードが保存されていないことを検証
        $this->assertDatabaseMissing('tags', [
            'name' => '例外テストタグ'
        ]);
    }

    // タグが、正しく削除（複数）されることをテスト
    public function testDestroyTagController()
    {
        // 3件のタグを作成
        $tags = Tag::factory()->count(3)->create(['user_id' => $this->user->id]);
        // 作成したタグのIDを、配列として取得
        $tagsId = $tags->pluck('id')->toArray();
        // 削除するタグのID（複数）のデータを作成
        $requestData = ['tags' => $tagsId];

        // タグを削除（複数）するの為に、リクエストを送信
        $response = $this->delete(route('user.tag.destroy'), $requestData);

        // リダイレクトで成功メッセージがフラッシュされていることを検証
        $response->assertRedirect(route('user.tag.index'));
        $response->assertSessionHas(['message' => '正常にタグを削除しました。', 'status' => 'success']);

        // タグが削除されたことを確認
        foreach ($tagsId as $tagId) {
            $this->assertDatabaseMissing('tags', ['id' => $tagId]);
        }
    }

    // タグが、正しく削除（複数）される時のエラーハンドリングをテスト
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function testErrorDestroyTagController()
    {
        // 2件のタグを作成
        $tags = Tag::factory()->count(2)->create(['user_id' => $this->user->id]);
        // 作成したタグのIDを、配列として取得
        $tagsId = $tags->pluck('id')->toArray();
        // 削除するタグのID（複数）のデータを作成
        $requestData = ['tags' => $tagsId];

        // TagService::deleteTags が例外を投げるようにエイリアスモック
        $tagServiceMock = Mockery::mock('alias:App\\Services\\TagService');
        $tagServiceMock->shouldReceive('deleteTags')
            ->once()->andThrow(new Exception('forced error'));

        // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
        Log::shouldReceive('error')->once()->withAnyArgs();

        // タグ削除のリクエストを送信
        $response = $this->from(route('user.tag.index'))->delete(route('user.tag.destroy'), $requestData);

        // リダイレクトでエラーがフラッシュされていることを検証
        $response->assertRedirect(route('user.tag.index'));
        $response->assertSessionHas(['message' => 'タグの削除に失敗しました。', 'status' => 'error']);

        // レコードが削除されていないことを検証
        foreach ($tags as $tag) {
            $this->assertDatabaseHas('tags', [
                'id' => $tag->id,
            ]);
        }
    }
}
