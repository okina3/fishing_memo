<?php

namespace Tests\User\Feature\Controllers;

use App\Models\Bait;
use App\Models\FishName;
use App\Models\Spot;
use App\Models\User;
use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use Mockery;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Tests\User\TestCase;

class MastersControllerTest extends TestCase
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

    // マスターズ管理画面の一覧が正しく表示されることをテスト
    public function testIndexMastersController()
    {
        // テストデータの作成
        Spot::factory()->count(2)->create(['user_id' => $this->user->id]);
        Bait::factory()->count(2)->create(['user_id' => $this->user->id]);
        FishName::factory()->count(2)->create(['user_id' => $this->user->id]);

        // 認証済みユーザーでマスターズ管理一覧ルートへアクセス
        $response = $this->get(route('user.masters.index', ['tab' => 'spots']));

        // ステータスコード200（OK）であることを検証
        $response->assertOk();
        // 返却されるビューが期待通り（user.masters.index）であることを検証
        $response->assertViewIs('user.masters.index');
        // ビューに渡される主要なデータ（全タブ・全釣り場・全エサ・全魚名・キーワード）が存在することを検証
        $response->assertViewHasAll(['tab', 'spots', 'baits', 'fishNames', 'keyword']);
    }

    // マスターズ管理画面から、釣り場を保存するテスト
    public function testStoreSpotFromMastersController()
    {
        // リクエストデータを作成
        $payload = ['spot_name' => 'マスター追加スポット'];

        // 釣り場を保存するの為に、リクエスト送信
        $response = $this->post(route('user.masters.spot.store'), $payload);

        // リダイレクトで成功メッセージがフラッシュされていることを検証
        $response->assertRedirect(route('user.masters.index', ['tab' => 'spots']));
        $response->assertSessionHas(['message' => '釣り場を追加しました。', 'status' => 'success']);

        // 釣り場が作成されていることを検証
        $this->assertDatabaseHas('spots', [
            'user_id' => $this->user->id,
            'name' => 'マスター追加スポット',
        ]);
    }

    // マスターズ管理画面から、釣り場を保存する時のエラーハンドリングをテスト
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function testErrorStoreSpotFromMastersController()
    {
        // リクエストデータを作成
        $payload = ['spot_name' => '失敗スポット'];

        // SpotService::createSpot が例外を投げるようにエイリアスモック
        $serviceMock = Mockery::mock('alias:App\\Services\\SpotService');
        $serviceMock->shouldReceive('createSpot')->once()->andThrow(new Exception('DBエラー'));

        // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
        Log::shouldReceive('error')->once()->withAnyArgs();

        // 釣り場を保存するの為に、リクエスト送信
        $response = $this->from(route('user.masters.index', ['tab' => 'spots']))
            ->post(route('user.masters.spot.store'), $payload);

        // リダイレクトでエラーメッセージがフラッシュされていることを検証
        $response->assertRedirect(route('user.masters.index', ['tab' => 'spots']));
        $response->assertSessionHas(['message' => '釣り場の追加に失敗しました', 'status' => 'error']);

        // レコードが保存されていないことを検証
        $this->assertDatabaseMissing('spots', [
            'user_id' => $this->user->id,
            'name' => '失敗スポット',
        ]);
    }

    // マスターズ管理画面から、エサを保存するテスト
    public function testStoreBaitFromMastersController()
    {
        // リクエストデータを作成
        $payload = ['bait_name' => 'マスター追加エサ'];

        // エサを保存するの為に、リクエスト送信
        $response = $this->post(route('user.masters.bait.store'), $payload);

        // リダイレクトで成功メッセージがフラッシュされていることを検証
        $response->assertRedirect(route('user.masters.index', ['tab' => 'baits']));
        $response->assertSessionHas(['message' => 'エサを追加しました。', 'status' => 'success']);

        // エサが作成されていることを検証
        $this->assertDatabaseHas('baits', [
            'user_id' => $this->user->id,
            'name' => 'マスター追加エサ',
        ]);
    }

    // マスターズ管理画面から、エサを保存する時のエラーハンドリングをテスト
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function testErrorStoreBaitFromMastersController()
    {
        // リクエストデータを作成
        $payload = ['bait_name' => '失敗エサ'];

        // BaitService::createBait が例外を投げるようにエイリアスモック
        $serviceMock = Mockery::mock('alias:App\\Services\\BaitService');
        $serviceMock->shouldReceive('createBait')->once()->andThrow(new Exception('DBエラー'));

        // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
        Log::shouldReceive('error')->once()->withAnyArgs();

        // エサを保存するの為に、リクエスト送信
        $response = $this->from(route('user.masters.index', ['tab' => 'baits']))
            ->post(route('user.masters.bait.store'), $payload);

        // リダイレクトでエラーメッセージがフラッシュされていることを検証
        $response->assertRedirect(route('user.masters.index', ['tab' => 'baits']));
        $response->assertSessionHas(['message' => 'エサの追加に失敗しました', 'status' => 'error']);

        // レコードが保存されていないことを検証
        $this->assertDatabaseMissing('baits', [
            'user_id' => $this->user->id,
            'name' => '失敗エサ',
        ]);
    }

    // マスターズ管理画面から、魚名を保存するテスト
    public function testStoreFishNameFromMastersController()
    {
        // リクエストデータを作成
        $payload = ['fish_name' => 'マスター追加魚名'];

        // 魚名を保存するの為に、リクエスト送信
        $response = $this->post(route('user.masters.fish-name.store'), $payload);

        // リダイレクトで成功メッセージがフラッシュされていることを検証
        $response->assertRedirect(route('user.masters.index', ['tab' => 'fishNames']));
        $response->assertSessionHas(['message' => '魚名を追加しました。', 'status' => 'success']);

        // 魚名が作成されていることを検証
        $this->assertDatabaseHas('fish_names', [
            'user_id' => $this->user->id,
            'name' => 'マスター追加魚名',
        ]);
    }

    // マスターズ管理画面から、魚名を保存する時のエラーハンドリングをテスト
    #[\PHPUnit\Framework\Attributes\RunInSeparateProcess]
    #[\PHPUnit\Framework\Attributes\PreserveGlobalState(false)]
    public function testErrorStoreFishNameFromMastersController()
    {
        // リクエストデータを作成
        $payload = ['fish_name' => '失敗魚名'];

        // FishNameService::createFishName が例外を投げるようにエイリアスモック
        $serviceMock = Mockery::mock('alias:App\\Services\\FishNameService');
        $serviceMock->shouldReceive('createFishName')->once()->andThrow(new Exception('DBエラー'));

        // Log::errorメソッドが呼び出されるときに、例外がログに記録されることを確認
        Log::shouldReceive('error')->once()->withAnyArgs();

        // 魚名を保存するの為に、リクエスト送信
        $response = $this->from(route('user.masters.index', ['tab' => 'fishNames']))
            ->post(route('user.masters.fish-name.store'), $payload);

        // リダイレクトでエラーメッセージがフラッシュされていることを検証
        $response->assertRedirect(route('user.masters.index', ['tab' => 'fishNames']));
        $response->assertSessionHas(['message' => '魚名の追加に失敗しました', 'status' => 'error']);

        // レコードが保存されていないことを検証
        $this->assertDatabaseMissing('fish_names', [
            'user_id' => $this->user->id,
            'name' => '失敗魚名',
        ]);
    }
}
