<?php

namespace Tests\User\Feature\Controllers;

use App\Models\Bait;
use App\Models\FishName;
use App\Models\Image;
use App\Models\Memo;
use App\Models\ShareSetting;
use App\Models\Tag;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\User\TestCase;

class ShareSettingControllerTest extends TestCase
{
    use RefreshDatabase;
    use MockeryPHPUnitIntegration;

    private User $user;
    private User $secondaryUser;
    private User $tertiaryUser;

    // テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
    protected function setUp(): void
    {
        // 親クラスのsetUpメソッドを呼び出し
        parent::setUp();
        // ユーザーを作成
        $this->user = User::factory()->create();
        // 2人目の別のユーザーを作成
        $this->secondaryUser = User::factory()->create();
        // 3人目のユーザーを作成
        $this->tertiaryUser = User::factory()->create();
        // 認証済みのユーザーを返す
        $this->actingAs($this->user, 'users');
    }

    // 自分に共有されたメモの一覧が正しく表示されることをテスト
    public function testIndexShareSettingController()
    {
        // 他人ユーザーのメモの作成
        $memo = Memo::factory()->create(['user_id' => $this->secondaryUser->id]);

        // 共有設定作成（他ユーザーが、自分へ共有している想定のレコード）
        ShareSetting::factory()->create([
            // 共有対象のユーザーID（自分）
            'sharing_user_id' => $this->user->id,
            // 共有対象メモID（他ユーザーのメモ）
            'memo_id' => $memo->id,
            // 共有の編集権限（0=なし / 1=あり）
            'edit_access' => 1,
        ]);

        // 認証済みユーザーで共有メモ一覧ルートへアクセス
        $response = $this->get(route('user.share-setting.index'));

        // ステータスコード200（OK）であることを検証
        $response->assertOk();
        // 返却されるビューが期待通り（user.shareSettings.index）であることを検証
        $response->assertViewIs('user.shareSettings.index');
        // ビューに渡される主要なデータ（共有メモ・共有ユーザー）が存在することを検証
        $response->assertViewHasAll(['shared_memos', 'shared_users']);
    }

    // 自分のメモの共有設定が正しく保存されることをテスト
    public function testStoreShareSettingController()
    {
        // 自分のメモの作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);

        // リクエストデータを作成（自分のメモを、他人に共有）
        $payload = [
            // 共有対象ユーザーのメールアドレス（他人メールアドレス）
            'share_user_start' => $this->secondaryUser->email,
            // 共有対象メモID（自分のメモ）
            'memoId' => $memo->id,
            // 共有の編集権限（0=なし / 1=あり）
            'edit_access' => 0,
        ];

        // 共有設定を保存する為に、リクエストを送信
        $response = $this->post(route('user.share-setting.store'), $payload);

        // リダイレクトで成功メッセージがフラッシュされていることを検証
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas(['message' => 'メモを共有しました。', 'status' => 'success']);

        // 共有設定が作成されていることを検証
        $this->assertDatabaseHas('share_settings', [
            'sharing_user_id' => $this->secondaryUser->id,
            'memo_id' => $memo->id,
            'edit_access' => 0,
        ]);
    }

    // 自分のメモの共有設定の保存時に正しくエラーハンドリングされることをテスト
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function testErrorStoreShareSettingController()
    {
        // 自分のメモの作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);

        // リクエストデータを作成（自分のメモを、他人に共有）
        $payload = [
            // 共有対象ユーザーのメールアドレス（他人メールアドレス）
            'share_user_start' => $this->secondaryUser->email,
            // 共有対象メモID（自分のメモ）
            'memoId' => $memo->id,
            // 共有の編集権限（0=なし / 1=あり）
            'edit_access' => 0,
        ];

        // ShareSettingService::createSetting が例外を投げるようにモック（resetDuplicateShareSettings は通過）
        $serviceMock = Mockery::mock('alias:App\\Services\\ShareSettingService');
        $serviceMock->shouldReceive('resetDuplicateShareSettings')->andReturnNull();
        $serviceMock->shouldReceive('createSetting')
            ->once()->andThrow(new Exception('DBエラー'));

        // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
        Log::shouldReceive('error')->once()->withAnyArgs();

        // 共有設定保存のリクエスト送信
        $response = $this->from(route('user.index'))
            ->post(route('user.share-setting.store'), $payload);

        // リダイレクトでエラーがフラッシュされていることを検証
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas(['message' => '共有の登録に失敗しました。', 'status' => 'error']);

        // レコードが保存されていないことを検証
        $this->assertDatabaseMissing('share_settings', [
            'sharing_user_id' => $this->secondaryUser->id,
            'memo_id' => $memo->id,
            'edit_access' => 0,
        ]);
    }

    // 自分に共有されたメモの詳細が正しく表示されることをテスト
    public function testShowShareSettingController()
    {
        // 共有設定作成（他ユーザーが、自分へ共有している想定のレコード）
        $memo = Memo::factory()->create(['user_id' => $this->secondaryUser->id]);
        $bait = Bait::factory()->create(['user_id' => $this->secondaryUser->id]);
        $fish = FishName::factory()->create(['user_id' => $this->secondaryUser->id]);
        $tag = Tag::factory()->create(['user_id' => $this->secondaryUser->id]);
        $image = Image::factory()->create(['user_id' => $this->secondaryUser->id]);

        // 他人メモに関連データを紐付け
        $memo->baits()->attach($bait->id);
        $memo->fish_names()->attach($fish->id, ['count' => 1, 'length' => 10]);
        $memo->tags()->attach($tag->id);
        $memo->images()->attach($image->id);

        // 共有設定作成（他ユーザーが、自分へ共有している想定のレコード）
        ShareSetting::factory()->create([
            // 共有対象のユーザーID（自分）
            'sharing_user_id' => $this->user->id,
            // 共有対象メモID（他ユーザーのメモ）
            'memo_id' => $memo->id,
            // 共有の編集権限（0=なし / 1=あり）
            'edit_access' => 0,
        ]);

        // 自分に共有されたメモの詳細画面を表示する為に、リクエストを送信
        $response = $this->get(route('user.share-setting.show', ['share' => $memo->id]));

        // ステータスコード200（OK）であることを検証
        $response->assertOk();
        // 返却されるビューが期待通り（user.shareSettings.show）であることを検証
        $response->assertViewIs('user.shareSettings.show');
        // ビューに渡される主要なデータ（選択メモ・エサ・魚名・タグ・画像・共有ユーザー名）が存在することを検証
        $response->assertViewHasAll([
            'select_memo',
            'get_memo_baits_name',
            'get_memo_fish_results',
            'get_memo_tags_name',
            'get_memo_images',
            'select_user',
        ]);
    }

    // 自分に共有されたメモの編集画面が正しく表示されることをテスト
    public function testEditShareSettingController()
    {
        // 共有設定作成（他ユーザーが、自分へ共有している想定のレコード）
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);
        $bait = Bait::factory()->create(['user_id' => $this->user->id]);
        $fish = FishName::factory()->create(['user_id' => $this->user->id]);
        $tag = Tag::factory()->create(['user_id' => $this->user->id]);
        $image = Image::factory()->create(['user_id' => $this->user->id]);

        // 他人メモに関連データを紐付け
        $memo->baits()->attach($bait->id);
        $memo->fish_names()->attach($fish->id, ['count' => 1, 'length' => 10]);
        $memo->tags()->attach($tag->id);
        $memo->images()->attach($image->id);

        // 共有設定作成（他ユーザーが、自分へ共有している想定のレコード）
        ShareSetting::factory()->create([
            // 共有対象のユーザーID（自分）
            'sharing_user_id' => $this->user->id,
            // 共有対象メモID（他ユーザーのメモ）
            'memo_id' => $memo->id,
            // 共有の編集権限（0=なし / 1=あり）
            'edit_access' => 1,
        ]);

        // 自分に共有されたメモの編集画面を表示する為に、リクエストを送信
        $response = $this->get(route('user.share-setting.edit', ['share' => $memo->id]));

        // ステータスコード200（OK）であることを検証
        $response->assertOk();
        // 返却されるビューが期待通り（user.shareSettings.edit）であることを検証
        $response->assertViewIs('user.shareSettings.edit');
        // ビューに渡される主要なデータ（選択メモ・エサ・魚名・タグ・画像・共有ユーザー名）が存在することを検証
        $response->assertViewHasAll([
            'select_memo',
            'get_memo_baits_name',
            'get_memo_fish_results',
            'get_memo_tags_name',
            'get_memo_images',
            'select_user',
        ]);
    }

    // 自分に共有されたメモが正しく更新されることをテスト
    public function testUpdateShareSettingController()
    {
        // 他人ユーザーのメモの作成
        $memo = Memo::factory()->create(['user_id' => $this->secondaryUser->id, 'content' => 'before']);

        // 更新用のリクエストデータを作成
        $payload = [
            'memoId' => $memo->id,
            'content' => 'after',
        ];

        // 共有設定作成（他ユーザーが、自分へ共有している想定のレコード）
        ShareSetting::factory()->create([
            // 共有対象のユーザーID（自分）
            'sharing_user_id' => $this->user->id,
            // 共有対象メモID（他ユーザーのメモ）
            'memo_id' => $memo->id,
            // 共有の編集権限（0=なし / 1=あり）
            'edit_access' => 1,
        ]);

        // 自分に共有されたメモを更新する為に、リクエストを送信
        $response = $this->patch(route('user.share-setting.update'), $payload);

        // リダイレクトで成功メッセージがフラッシュされていることを検証
        $response->assertRedirect(route('user.share-setting.index'));
        $response->assertSessionHas(['message' => '共有されたメモを更新しました。', 'status' => 'success']);

        // メモが更新されていることを検証
        $this->assertDatabaseHas('memos', [
            'id' => $memo->id,
            'content' => 'after',
        ]);
    }

    // 自分の共有メモが正しく削除されることをテスト
    public function testDestroyShareSettingController()
    {
        // 自分のメモの作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);

        // リクエストデータを作成（自分のメモを、他人に共有）
        $payload = [
            // 共有対象ユーザーのメールアドレス（他人メールアドレス）
            'share_user_end' => $this->secondaryUser->email,
            // 共有対象メモID（自分のメモ）
            'memoId' => $memo->id,
            // 共有の編集権限（0=なし / 1=あり）
            'edit_access' => 0,
        ];

        // 共有設定を削除する為に、リクエストを送信
        $response = $this->delete(route('user.share-setting.destroy'), $payload);

        // リダイレクトで成功メッセージがフラッシュされていることを検証
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas(['message' => '共有を解除しました。', 'status' => 'success']);

        // 共有設定が削除されたことを確認
        $this->assertDatabaseMissing('share_settings', [
            'sharing_user_id' => $this->secondaryUser->id,
            'memo_id' => $memo->id,
        ]);
    }
}
