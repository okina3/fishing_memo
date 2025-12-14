<?php

namespace Tests\User\Feature\Services;

use App\Models\Bait;
use App\Models\FishName;
use App\Models\Image;
use App\Models\Memo;
use App\Models\ShareSetting;
use App\Models\Tag;
use App\Models\User;
use App\Services\MemoService;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\User\TestCase;

class MemoServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $user;
    private User $secondaryUser;

    // テスト前の初期設定（各テストメソッドの実行前に毎回呼び出される）
    protected function setUp(): void
    {
        // 親クラスのsetUpメソッドを呼び出し
        parent::setUp();
        // ユーザーを作成
        $this->user = User::factory()->create();
        // 2人目の別のユーザーを作成
        $this->secondaryUser = User::factory()->create();
        // 認証済みのユーザーを返す
        $this->actingAs($this->user, 'users');
    }

    // タグを作成するヘルパーメソッド
    private function createTags(int $count): Collection
    {
        return Tag::factory()->count($count)->create(['user_id' => $this->user->id]);
    }


    // メモを共有させる設定を作成するヘルパーメソッド
    private function createShareSetting(User $sharingUser, Memo $memo): ShareSetting
    {
        return ShareSetting::factory()->create([
            // 共有させたいユーザー
            'sharing_user_id' => $sharingUser->id,
            // メモの選択
            'memo_id' => $memo->id,
            // 編集も可能
            'edit_access' => true
        ]);
    }

    // 別のユーザーのメモを見られなくするメソッドのテスト
    public function testCheckUserMemo()
    {
        // 2人目の別のユーザーを作成
        $secondaryUser = User::factory()->create();
        // 別のユーザーに関連するメモを一件作成
        $secondaryUserMemo = Memo::factory()->create(['user_id' => $secondaryUser->id]);

        // リクエストを作成
        $request = Request::create('/memos/' . $secondaryUserMemo->id);
        // リクエストのルートを設定
        $request->setRouteResolver(function () use ($request) {
            // 新しいルートオブジェクトを作成し、リクエストにバインド
            return (new Route('GET', '/memos/{memo}', []))->bind($request);
        });

        // 異常なユーザーのメモアクセスは、例外の発生を期待（404エラー）
        $this->expectException(NotFoundHttpException::class);
        // メモの所有者を確認するサービスメソッドを実行
        MemoService::checkUserMemo($request);
    }

    // 全メモ、また、検索したメモを一覧表示するメソッドのテスト
    public function testSearchMemos()
    {
        // 4件の自分のメモを作成
        Memo::factory()->count(4)->create(['user_id' => $this->user->id]);
        // 1件の自分のメモを作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);
        // 1件の自分のタグを作成
        $tag = $this->createTags(1)->first();
        // 1件の共有設定を作成（自分のメモを、2人目のユーザーに共有）
        $this->createShareSetting($this->secondaryUser, $memo);

        // メモにタグを関連付ける
        $memo->tags()->attach($tag);
        // タグのIDをクエリパラメータに設定
        request()->query->set('tag', $tag->id);
        // メモを検索するサービスメソッドを実行
        $response = MemoService::searchMemos();

        // 期待されるメモの数が、1であることを確認
        $this->assertCount(1, $response);

        // 共有設定を確認
        $sharedMemo = $response->firstWhere('id', $memo->id);
        $this->assertNotNull($sharedMemo, 'Shared memo not found in the response');
        $this->assertEquals('共有中', $sharedMemo->status ?? null);

        // タグのIDをクエリパラメータから削除
        request()->query->remove('tag');
        // 再度メモを検索するサービスメソッドを実行（タグなし）
        $response = MemoService::searchMemos();

        // 期待されるメモの数が、5であることを確認
        $this->assertCount(5, $response);
    }

    // メモ保存機能のテスト
    public function testCreateMemo()
    {
        // メモ作成のためのリクエストを作成
        $request = new Request([
            'fishing_date' => '2025-01-02',
            'start_time' => '06:00',
            'end_time' => '09:00',
            'weather' => '晴れ',
            'air_temp' => '20',
            'wind_dir' => '北',
            'content' => '新規メモの内容',
        ]);

        // サービスを使ってメモを作成
        $created = MemoService::createMemo($request);

        // DB にレコードが存在することを確認
        $this->assertDatabaseHas('memos', [
            'id' => $created->id,
            'user_id' => $this->user->id,
            'content' => '新規メモの内容',
        ]);
    }

    // メモに紐づいたエサを、中間テーブルに保存するメソッドのテスト
    public function testAttachExistingBaits()
    {
        // 1件の自分メモを作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);
        // 2件の自分のエサを作成
        $baits = Bait::factory()->count(2)->create(['user_id' => $this->user->id]);

        // リクエストを作成してサービスを呼び出す
        $request = new Request(['baits' => $baits->pluck('id')->toArray()]);
        MemoService::attachExistingBaits($request, $memo->id);

        // 中間テーブルに関連付けが保存されていることを確認
        foreach ($baits as $bait) {
            $this->assertDatabaseHas('memo_baits', [
                'memo_id' => $memo->id,
                'bait_id' => $bait->id,
            ]);
        }
    }

    // メモに紐づいた釣果データを、中間テーブルに保存するメソッドのテスト
    public function testAttachExistingFishNames()
    {
        // 1件の自分メモを作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);
        // 2件の自分の魚名を作成
        $fishNames = FishName::factory()->count(2)->create(['user_id' => $this->user->id]);

        // fishing_results の形式でリクエストを作成
        $fishingResults = [
            ['fish_name_id' => $fishNames[0]->id, 'count' => 3, 'length' => 30],
            ['fish_name_id' => $fishNames[1]->id, 'count' => 1, 'length' => 10],
        ];

        // リクエストを作成してサービスを呼び出す
        $request = new Request(['fishing_results' => $fishingResults]);
        MemoService::attachExistingFishNames($request, $memo->id);

        // 中間テーブルに関連付けが保存されていることを確認
        $this->assertDatabaseHas('memo_fish_names', [
            'memo_id' => $memo->id,
            'fish_name_id' => $fishNames[0]->id,
            'count' => 3,
            'length' => 30,
        ]);

        // 中間テーブルに関連付けが保存されていることを確認
        $this->assertDatabaseHas('memo_fish_names', [
            'memo_id' => $memo->id,
            'fish_name_id' => $fishNames[1]->id,
            'count' => 1,
            'length' => 10,
        ]);
    }

    // メモに紐づいた釣果データを、中間テーブルに保存するメソッドのテスト（空の釣果データ）
    public function testAttachExistingFishNames_withEmptyResults()
    {
        // 1件の自分メモを作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);
        // 空の釣果データでリクエストを作成してサービスを呼び出す
        $request = new Request(['fishing_results' => []]);
        MemoService::attachExistingFishNames($request, $memo->id);

        // 中間テーブルに関連付けが保存されていないことを確認
        $this->assertDatabaseMissing('memo_fish_names', ['memo_id' => $memo->id]);
    }

    // メモに紐づいた釣果データを、中間テーブルに保存するメソッドのテスト（無効な魚名IDを含む場合）
    public function testAttachExistingFishNames_skipsInvalidEntries()
    {
        // 1件の自分メモを作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);
        // 2件の自分の魚名を作成
        $fishNames = FishName::factory()->count(2)->create(['user_id' => $this->user->id]);

        // fishing_results の形式でリクエストを作成（1件は無効な魚名ID）
        $fishingResults = [
            ['fish_name_id' => 0, 'count' => 2, 'length' => 20],
            ['fish_name_id' => $fishNames[1]->id, 'count' => 1, 'length' => 10], // valid -> should be attached
        ];

        // リクエストを作成してサービスを呼び出す
        $request = new Request(['fishing_results' => $fishingResults]);
        MemoService::attachExistingFishNames($request, $memo->id);

        // 中間テーブルに関連付けが保存されていることを確認
        $this->assertDatabaseHas('memo_fish_names', [
            'memo_id' => $memo->id,
            'fish_name_id' => $fishNames[1]->id,
            'count' => 1,
            'length' => 10,
        ]);

        // 無効な魚名IDに対する関連付けが行われていないことを確認
        $this->assertDatabaseMissing('memo_fish_names', [
            'memo_id' => $memo->id,
            'fish_name_id' => 0,
        ]);
    }

    // メモに紐づいた既存のタグを、中間テーブルに保存するメソッドのテスト
    public function testAttachExistingTags()
    {
        // 1件の自分メモを作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);
        // 2件の自分のタグを作成
        $tags = $this->createTags(2);

        // リクエストを作成してサービスを呼び出す
        $request = new Request(['tags' => $tags->pluck('id')->toArray()]);
        MemoService::attachExistingTags($request, $memo->id);

        // 中間テーブルに関連付けが保存されていることを確認
        foreach ($tags as $tag) {
            $this->assertDatabaseHas('memo_tags', [
                'memo_id' => $memo->id,
                'tag_id' => $tag->id,
            ]);
        }
    }

    // メモに紐づいた既存画像を、中間テーブルに値を保存するメソッドのテスト
    public function testAttachExistingImages()
    {
        // 1件の自分メモを作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);
        // 2件の自分の画像を作成
        $images = Image::factory()->count(2)->create(['user_id' => $this->user->id]);

        // リクエストを作成してサービスを呼び出す
        $request = new Request(['images' => $images->pluck('id')->toArray()]);
        MemoService::attachExistingImages($request, $memo->id);

        // 中間テーブルに関連付けが保存されていることを確認
        foreach ($images as $image) {
            $this->assertDatabaseHas('memo_images', [
                'memo_id' => $memo->id,
                'image_id' => $image->id,
            ]);
        }
    }

    // メモ更新機能のテスト
    public function testUpdateMemo()
    {
        // 1件の自分のメモを作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);

        // メモ更新のためのリクエストを作成
        $request = new Request([
            'memoId' => $memo->id,
            'fishing_date' => $memo->fishing_date,
            'start_time' => $memo->start_time,
            'end_time' => $memo->end_time,
            'fishing_spot' => $memo->spot_id,
            'weather' => $memo->weather,
            'air_temp' => $memo->air_temp,
            'wind_dir' => $memo->wind_dir,
            'content' => '更新された内容',
        ]);
        // メモを更新のサービスメソッドを実行
        $updatedMemo = MemoService::updateMemo($request);

        // メモの内容が更新されていることを確認
        $this->assertEquals('更新された内容', $updatedMemo->content);
    }

    // メモ共有状態チェック機能のテスト
    public function testCheckShared()
    {
        // 1件の自分のメモを作成
        $memo = Memo::factory()->create(['user_id' => $this->user->id]);
        // 共有設定を作成（自分のメモを、2人目のユーザーに共有）
        $this->createShareSetting($this->secondaryUser, $memo);
        // メモの共有状態をチェックのサービスメソッドを実行
        $checkedMemo = MemoService::checkShared($memo);

        // メモのステータスが「共有中」となっていることを確認
        $this->assertEquals('共有中', $checkedMemo->status);
    }
}
