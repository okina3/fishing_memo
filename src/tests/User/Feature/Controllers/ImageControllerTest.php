<?php

namespace Tests\User\Feature\Controllers;

use App\Models\Image;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\User\TestCase;

class ImageControllerTest extends TestCase
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

    // 画像の一覧が、正しく表示されることをテスト
    public function testIndexImageController()
    {
        // 認証済みユーザーで画像一覧ルートへアクセス
        $response = $this->get(route('user.image.index'));

        // ステータスコード200（OK）であることを検証
        $response->assertOk();
        // 返却されるビューが期待通り（user.images.index）であることを検証
        $response->assertViewIs('user.images.index');
        // ビューに渡される主要なデータ（全画像）が存在することを検証
        $response->assertViewHasAll(['all_images']);
    }

    // 画像の新規登録画面が、正しく表示されることをテスト
    public function testCreateImageController()
    {
        // 認証済みユーザーで画像登録画面へアクセス
        $response = $this->get(route('user.image.create'));

        // ステータスコード200（OK）であることを検証
        $response->assertOk();
        // 返却されるビューが期待通り（user.images.create）であることを検証
        $response->assertViewIs('user.images.create');
    }

    // 画像が、正しく保存されることをテスト
    public function testStoreImageController()
    {
        // ストレージのフェイク
        Storage::fake('public');

        // テスト用のアップロードファイルを作成
        $file = UploadedFile::fake()->image('test_image.jpg');

        // ブラウザバック対策用のセッション設定
        Session::put('back_button_clicked', encrypt(config('common_browser_back.browser_back_key')));

        // 画像を保存するの為に、リクエストを送信
        $response = $this->post(route('user.image.store'), ['images' => $file]);

        // リダイレクトで成功メッセージがフラッシュされていることを検証
        $response->assertRedirect(route('user.image.index'));
        $response->assertSessionHas(['message' => '画像を登録しました。', 'status' => 'success']);

        // 最新のレコードを取得。
        $stored = Image::query()->where('user_id', $this->user->id)->latest('id')->first();
        // DB にレコードが存在することを確認
        $this->assertNotNull($stored, 'Image record not found in database');
        // 取得したレコードに filename が設定されていることを確認
        $this->assertNotEmpty($stored->filename, 'Stored image filename is empty');
        // Storageにファイルが保存されているかを検証
        $this->assertTrue(Storage::disk('public')->exists($stored->filename), 'Stored file not found in storage');
    }

    // 画像が、正しく保存される時のエラーハンドリングをテスト
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function testErrorStoreImageController()
    {
        // ストレージのフェイク
        Storage::fake('public');

        // テスト用のアップロードファイルを作成
        $file = UploadedFile::fake()->image('test_image.jpg');

        // ブラウザバック対策用のセッション設定
        Session::put('back_button_clicked', encrypt(config('common_browser_back.browser_back_key')));

        // ImageService::afterResizingImage が例外を投げるようにエイリアスモック（checkUserImage は通過）
        $imageServiceMock = Mockery::mock('alias:App\\Services\\ImageService');
        $imageServiceMock->shouldReceive('checkUserImage')->andReturnNull();
        $imageServiceMock->shouldReceive('afterResizingImage')
            ->once()->andThrow(new Exception('DBエラー'));

        // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
        Log::shouldReceive('error')->once()->withAnyArgs();

        // 画像を保存するの為に、リクエストを送信
        $response = $this->from(route('user.image.create'))->post(route('user.image.store'), ['images' => $file]);

        // リダイレクトでエラーがフラッシュされていることを検証
        $response->assertRedirect(route('user.image.create'));
        $response->assertSessionHas(['message' => '画像の登録に失敗しました。', 'status' => 'error']);

        // レコードが保存されていないことを検証
        $this->assertDatabaseMissing('images', [
            'user_id' => $this->user->id,
        ]);
    }

    // 画像の詳細が、正しく表示されることをテスト
    public function testShowImageController()
    {
        // 自分の画像を1件作成
        $image = Image::factory()->create(['user_id' => $this->user->id]);

        // 画像詳細画面を表示する為に、リクエストを送信
        $response = $this->get(route('user.image.show', $image->id));

        // ステータスコード200（OK）であることを検証
        $response->assertStatus(200);
        // 返却されるビューが期待通り（user.images.show）であることを検証
        $response->assertViewIs('user.images.show');
        // ビューに渡される主要なデータ（選択画像）が存在することを検証
        $response->assertViewHasAll(['select_image']);
    }

    // 画像が、正しく削除されることをテスト
    public function testDestroyImageController()
    {
        // 1件の画像を作成
        $image = Image::factory()->create(['user_id' => $this->user->id]);

        // 画像を削除する為に、リクエストを送信
        $response = $this->delete(route('user.image.destroy', ['imageId' => $image->id]));

        // リダイレクトで成功メッセージがフラッシュされていることを検証
        $response->assertRedirect(route('user.image.index'));
        $response->assertSessionHas(['message' => '正常に画像を削除しました。', 'status' => 'success']);

        // 画像が削除されたことを確認
        $this->assertDatabaseMissing('images', [
            'id' => $image->id
        ]);
    }

    // 画像が、正しく削除される時のエラーハンドリングをテスト
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function testErrorDestroyImageController()
    {
        // 1件の画像を作成
        $image = Image::factory()->create(['user_id' => $this->user->id]);

        // ImageService::deleteStorage が例外を投げるようにエイリアスモック（checkUserImage は通過）
        $imageServiceMock = Mockery::mock('alias:App\\Services\\ImageService');
        $imageServiceMock->shouldReceive('checkUserImage')->andReturnNull();
        $imageServiceMock->shouldReceive('deleteStorage')
            ->once()->andThrow(new Exception('DBエラー'));

        // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
        Log::shouldReceive('error')->once()->withAnyArgs();

        // 画像を削除する為に、リクエストを送信
        $response = $this->from(route('user.image.index'))->delete(route('user.image.destroy', ['imageId' => $image->id]));

        // リダイレクトでエラーがフラッシュされていることを検証
        $response->assertRedirect(route('user.image.index'));
        $response->assertSessionHas(['message' => '画像の削除に失敗しました。', 'status' => 'error']);

        // レコードが削除されていないことを検証
        $this->assertDatabaseHas('images', [
            'id' => $image->id,
        ]);
    }
}
