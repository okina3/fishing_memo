<?php

namespace Tests\User\Feature\Controllers;

use App\Models\Bait;
use App\Models\FishName;
use App\Models\Spot;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
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
        // ビューに渡される主要なデータが存在することを検証
        $response->assertViewHasAll(['tab', 'spots', 'baits', 'fishNames', 'keyword']);
    }
}
