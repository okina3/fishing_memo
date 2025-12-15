<?php

namespace Tests\User\Feature\Controllers;

use App\Models\Bait;
use App\Models\FishName;
use App\Models\Image;
use App\Models\Memo;
use App\Models\Spot;
use App\Models\Tag;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\User\TestCase;

class MemoControllerTest extends TestCase
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

    // メモとタグの一覧が正しく表示されることをテスト
    public function testIndexMemoController()
    {
        // 認証済みユーザーでメモ一覧ルートへアクセス
        $response = $this->get(route('user.index'));

        // ステータスコード200（OK）であることを検証
        $response->assertOk();
        // 返却されるビューが期待通り（user.memos.index）であることを検証
        $response->assertViewIs('user.memos.index');
        // ビューに渡される主要なデータ（全メモ・全タグ）が存在することを検証
        $response->assertViewHasAll(['all_memos', 'all_tags']);
    }

    // メモの新規作成画面が、正しく表示されることをテスト
    public function testCreateMemoController()
    {
        // 認証済みユーザーでメモ作成画面へアクセス
        $response = $this->get(route('user.create'));

        // ステータスコード200（OK）であることを検証
        $response->assertOk();
        // 返却されるビューが期待通り（user.memos.create）であることを検証
        $response->assertViewIs('user.memos.create');
        // ビューに渡される主要なデータ（釣り場・エサ・魚名・タグ・画像）が存在することを検証
        $response->assertViewHasAll(['all_spots', 'all_baits', 'all_fish_names', 'all_tags', 'all_images']);
    }

    // メモが、正しく保存されることをテスト
    public function testStoreMemoController()
    {
        // 関連データを作成
        $spot = Spot::factory()->create(['user_id' => $this->user->id]);
        $bait = Bait::factory()->create(['user_id' => $this->user->id]);
        $fish = FishName::factory()->create(['user_id' => $this->user->id]);
        $tag = Tag::factory()->create(['user_id' => $this->user->id]);
        $image = Image::factory()->create(['user_id' => $this->user->id]);

        // リクエストデータを作成
        $payload = [
            'fishing_date' => now()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:30',
            'weather' => '晴れ',
            'air_temp' => 20,
            'wind_dir' => '北',
            'spot_areas' => [
                [
                    'spot_id' => $spot->id,
                    'river_flow' => '流れあり',
                    'turbidity' => 'クリア',
                    'water_level' => 1.2,
                    'water_temp' => 15,
                ],
            ],
            'baits' => [$bait->id],
            'fishing_results' => [
                [
                    'fish_name_id' => $fish->id,
                    'count' => 2,
                    'length' => 30,
                ],
            ],
            'tags' => [$tag->id],
            'images' => [$image->id],
            'new_tag' => '新規タグA',
            'content' => 'テストメモの内容',
        ];

        // ブラウザバック対策用のセッション設定
        Session::put('back_button_clicked', encrypt(config('common_browser_back.browser_back_key')));

        // メモを保存するの為に、リクエスト送信
        $response = $this->post(route('user.store'), $payload);

        // リダイレクトで成功メッセージがフラッシュされていることを検証
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas(['message' => 'メモを登録しました。', 'status' => 'success']);

        // メモが作成されていることを検証
        $memo = Memo::query()->where('user_id', $this->user->id)->latest('id')->first();
        $this->assertNotNull($memo);
        $this->assertSame('北', $memo->wind_dir);
        $this->assertSame('晴れ', $memo->weather);
        $this->assertSame('テストメモの内容', $memo->content);

        // メモと釣り場の中間データが作成されていることを検証
        $this->assertDatabaseHas('memo_spots', [
            'memo_id' => $memo->id,
            'spot_id' => $spot->id,
            'river_flow' => '流れあり',
            'turbidity' => 'クリア',
            'water_level' => 1.2,
            'water_temp' => 15,
        ]);

        // メモとエサの中間データが作成されていることを検証
        $this->assertDatabaseHas('memo_baits', [
            'memo_id' => $memo->id,
            'bait_id' => $bait->id,
        ]);

        // メモと魚名の中間データが作成されていることを検証
        $this->assertDatabaseHas('memo_fish_names', [
            'memo_id' => $memo->id,
            'fish_name_id' => $fish->id,
            'count' => 2,
            'length' => 30,
        ]);

        // メモとタグの中間データが作成されていることを検証
        $this->assertDatabaseHas('memo_tags', [
            'memo_id' => $memo->id,
            'tag_id' => $tag->id,
        ]);

        // メモと画像の中間データが作成されていることを検証
        $this->assertDatabaseHas('memo_images', [
            'memo_id' => $memo->id,
            'image_id' => $image->id,
        ]);

        // 新規タグが作成され、メモと紐づいていることを検証
        $createdNewTag = Tag::query()->where('name', '新規タグA')->where('user_id', $this->user->id)->first();
        $this->assertNotNull($createdNewTag);

        // メモと新規タグの中間データが作成されていることを検証
        $this->assertDatabaseHas('memo_tags', [
            'memo_id' => $memo->id,
            'tag_id' => $createdNewTag->id,
        ]);
    }

    // メモが、正しく保存される時のエラーハンドリングをテスト
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function testErrorStoreMemoController()
    {
        // 関連データを作成
        $spot = Spot::factory()->create(['user_id' => $this->user->id]);
        $bait = Bait::factory()->create(['user_id' => $this->user->id]);
        $fish = FishName::factory()->create(['user_id' => $this->user->id]);
        $tag = Tag::factory()->create(['user_id' => $this->user->id]);
        $image = Image::factory()->create(['user_id' => $this->user->id]);

        // リクエストデータを作成
        $payload = [
            'fishing_date' => now()->toDateString(),
            'start_time' => '09:00',
            'end_time' => '10:30',
            'weather' => '晴れ',
            'air_temp' => 20,
            'wind_dir' => '北',
            'spot_areas' => [
                [
                    'spot_id' => $spot->id,
                    'river_flow' => '流れあり',
                    'turbidity' => 'クリア',
                    'water_level' => 1.2,
                    'water_temp' => 15,
                ],
            ],
            'baits' => [$bait->id],
            'fishing_results' => [
                [
                    'fish_name_id' => $fish->id,
                    'count' => 2,
                    'length' => 30,
                ],
            ],
            'tags' => [$tag->id],
            'images' => [$image->id],
            'new_tag' => '新規タグA',
            'content' => 'テストメモの内容',
        ];

        // ブラウザバック対策用のセッション設定
        Session::put('back_button_clicked', encrypt(config('common_browser_back.browser_back_key')));

        // MemoService::createMemo が例外を投げるようにエイリアスモック（checkUserMemo は通過）
        $memoServiceMock = Mockery::mock('alias:App\\Services\\MemoService');
        $memoServiceMock->shouldReceive('checkUserMemo')->andReturnNull();
        $memoServiceMock->shouldReceive('createMemo')
            ->once()->andThrow(new Exception('DBエラー'));

        // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
        Log::shouldReceive('error')->once()->withAnyArgs();

        // メモを保存するの為に、リクエスト送信
        $response = $this->from(route('user.create'))->post(route('user.store'), $payload);

        // リダイレクトでエラーがフラッシュされていることを検証
        $response->assertRedirect(route('user.create'));
        $response->assertSessionHas(['message' => 'メモの登録に失敗しました。', 'status' => 'error']);

        // レコードが保存されていないことを検証
        $this->assertDatabaseMissing('memos', [
            'user_id' => $this->user->id,
            'content' => 'テストメモの内容',
        ]);
    }

    // メモの詳細表示が、正しく動作することをテスト
    public function testShowMemoController()
    {
        // 自分のメモを1件作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);

        // メモ詳細画面を表示する為に、リクエストを送信
        $response = $this->get(route('user.show', ['memo' => $memo->id]));

        // ステータスコード200（OK）であることを検証
        $response->assertOk();
        // 返却されるビューが期待通り（user.memos.show）であることを検証
        $response->assertViewIs('user.memos.show');
        // ビューに渡される主要なデータが存在することを検証
        $response->assertViewHasAll([
            'select_memo',
            'get_memo_spots_name',
            'get_memo_baits_name',
            'get_memo_fish_results',
            'get_memo_tags_name',
            'get_memo_images',
            'shared_users',
        ]);
    }

    // メモの編集画面が、正しく表示されることをテスト
    public function testEditMemoController()
    {
        // 自分のメモを1件作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);

        // メモの編集画面を表示する為に、リクエストを送信
        $response = $this->get(route('user.edit', ['memo' => $memo->id]));

        // ステータスコード200（OK）であることを検証
        $response->assertOk();
        // 返却されるビューが期待通り（user.memos.edit）であることを検証
        $response->assertViewIs('user.memos.edit');
        // ビューに渡される主要なデータが存在することを検証
        $response->assertViewHasAll([
            'all_spots',
            'all_baits',
            'all_fish_names',
            'all_tags',
            'all_images',
            'select_memo',
            'get_memo_tags_id',
            'get_memo_images_id',
            'get_memo_images',
        ]);
    }

    // メモが、正しく更新されることをテスト
    public function testUpdateMemoController()
    {
        // 自分のメモを1件作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);

        // 更新用のリクエストデータを作成
        $payload = [
            'memoId' => $memo->id,
            'fishing_date' => now()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:30',
            'weather' => '曇り',
            'air_temp' => 22,
            'wind_dir' => '南',
            'spot_areas' => [],
            'baits' => [],
            'fishing_results' => [],
            'tags' => [],
            'images' => [],
            'new_tag' => '',
            'content' => '更新後のメモ内容',
        ];

        // ブラウザバック対策用のセッション設定
        Session::put('back_button_clicked', encrypt(config('common_browser_back.browser_back_key')));

        // メモを更新するの為に、リクエスト送信
        $response = $this->patch(route('user.update'), $payload);

        // リダイレクトで成功メッセージがフラッシュされていることを検証
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas(['message' => 'メモを更新しました。', 'status' => 'success']);

        // メモが更新されていることを検証
        $this->assertDatabaseHas('memos', [
            'id' => $memo->id,
            'weather' => '曇り',
            'content' => '更新後のメモ内容',
        ]);
    }

    // メモが、正しく更新される時のエラーハンドリングをテスト
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function testErrorUpdateMemoController()
    {
        // 関連データを作成
        $spot = Spot::factory()->create(['user_id' => $this->user->id]);

        // 自分のメモを1件作成
        $memo = Memo::factory()->create([
            'user_id' => $this->user->id,
            'content' => '初期メモ内容',
        ]);

        // 更新用のリクエストデータを作成
        $payload = [
            'memoId' => $memo->id,
            'fishing_date' => now()->toDateString(),
            'start_time' => '10:00',
            'end_time' => '11:30',
            'weather' => '曇り',
            'air_temp' => 22,
            'wind_dir' => '南',
            'spot_areas' => [],
            'baits' => [],
            'fishing_results' => [],
            'tags' => [],
            'images' => [],
            'new_tag' => '',
            'content' => '更新後のメモ内容',
        ];

        // ブラウザバック対策用のセッション設定
        Session::put('back_button_clicked', encrypt(config('common_browser_back.browser_back_key')));

        // MemoService::updateMemo が例外を投げるようにエイリアスモック（checkUserMemo は通過）
        $memoServiceMock = Mockery::mock('alias:App\\Services\\MemoService');
        $memoServiceMock->shouldReceive('checkUserMemo')->andReturnNull();
        $memoServiceMock->shouldReceive('updateMemo')
            ->once()->andThrow(new Exception('DBエラー'));

        // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
        Log::shouldReceive('error')->once()->withAnyArgs();

        // メモを更新するの為に、リクエスト送信
        $response = $this->from(route('user.edit', ['memo' => $memo->id]))
            ->patch(route('user.update'), $payload);

        // リダイレクトでエラーがフラッシュされていることを検証
        $response->assertRedirect(route('user.edit', ['memo' => $memo->id]));
        $response->assertSessionHas(['message' => 'メモの更新に失敗しました。', 'status' => 'error']);

        // レコードが更新されていないことを検証
        $this->assertDatabaseMissing('memos', [
            'id' => $memo->id,
            'content' => '更新後のメモ内容',
        ]);
    }

    // メモが、正しく削除（ソフトデリート）されることをテスト
    public function testDestroyMemoController()
    {
        // 自分のメモを1件作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);

        // メモを削除する為に、リクエストを送信
        $response = $this->delete(route('user.destroy'), ['memoId' => $memo->id]);

        // リダイレクトで成功メッセージがフラッシュされていることを検証
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas(['message' => 'メモをゴミ箱に移動しました。', 'status' => 'success']);

        // 対象メモがソフトデリートされていることを検証
        $this->assertSoftDeleted('memos', ['id' => $memo->id]);
    }

    // メモが、正しく削除（ソフトデリート）される時のエラーハンドリングをテスト
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function testErrorDestroyMemoController()
    {
        // 自分のメモを1件作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);

        // ShareSettingService::deleteShareSettingAll が例外を投げるようにエイリアスモック（checkUserMemo は通過）
        $shareSettingServiceMock = Mockery::mock('alias:App\\Services\\ShareSettingService');
        $shareSettingServiceMock->shouldReceive('checkUserMemo')->andReturnNull();
        $shareSettingServiceMock->shouldReceive('deleteShareSettingAll')
            ->once()->andThrow(new Exception('DBエラー'));

        // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
        Log::shouldReceive('error')->once()->withAnyArgs();

        // メモを削除する為に、リクエストを送信
        $response = $this->from(route('user.index'))
            ->delete(route('user.destroy'), ['memoId' => $memo->id]);

        // リダイレクトでエラーがフラッシュされていることを検証
        $response->assertRedirect(route('user.index'));
        $response->assertSessionHas(['message' => 'メモの削除に失敗しました。', 'status' => 'error']);

        // レコードが削除されていないことを検証
        $this->assertDatabaseHas('memos', [
            'id' => $memo->id,
            'deleted_at' => null,
        ]);
    }
}
