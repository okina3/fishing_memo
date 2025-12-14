<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreMemoRequest;
use App\Models\Bait;
use App\Models\FishName;
use App\Models\Image;
use App\Models\Memo;
use App\Models\MemoBait;
use App\Models\MemoFishName;
use App\Models\MemoImage;
use App\Models\MemoSpot;
use App\Models\MemoTag;
use App\Models\Spot;
use App\Models\Tag;
use App\Services\BaitService;
use App\Services\FishNameService;
use App\Services\ImageService;
use App\Services\MemoService;
use App\Services\SessionService;
use App\Services\ShareSettingService;
use App\Services\SpotService;
use App\Services\TagService;
use Closure;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class MemoController extends Controller
{
    public function __construct()
    {
        // 別のユーザーのメモを見られなくする認証。
        $this->middleware(function (Request $request, Closure $next) {
            MemoService::checkUserMemo($request);
            return $next($request);
        });
    }

    /**
     * メモとタグの一覧を表示するメソッド。
     * @return View
     */
    public function index(): View
    {
        // ブラウザバック対策（値を削除する）
        SessionService::resetBrowserBackSession();
        // 全メモ、または検索されたメモを表示する
        $all_memos = MemoService::searchMemos();
        // 全タグを取得する
        $all_tags = Tag::availableAllTags()->get();

        return view('user.memos.index', compact('all_memos', 'all_tags'));
    }

    /**
     * メモの新規作成画面を表示するメソッド。
     * @return View
     */
    public function create(): View
    {
        // 全スポットを取得する
        $all_spots = Spot::availableAllSpots()->get();
        // 全エサを取得する
        $all_baits = Bait::availableAllBaits()->get();
        // 全魚名を取得する
        $all_fish_names = FishName::availableAllFishNames()->get();
        // 全タグを取得する
        $all_tags = Tag::availableAllTags()->get();
        // 全画像を取得する
        $all_images = Image::availableAllImages()->get();
        // ブラウザバック対策（値を持たせる）
        SessionService::setBrowserBackSession();

        return view('user.memos.create', compact('all_spots', 'all_baits', 'all_fish_names', 'all_tags', 'all_images'));
    }

    /**
     * メモを保存するメソッド。
     * @param StoreMemoRequest $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function store(StoreMemoRequest $request): RedirectResponse
    {
        // ブラウザバック対策（値を確認）
        SessionService::clickBrowserBackSession();
        try {
            DB::transaction(function () use ($request) {
                // メモを保存
                $memo = MemoService::createMemo($request);
                // 釣り場を、メモに紐付けて中間テーブルに保存
                MemoService::attachExistingSpots($request, $memo->id);
                // エサを、メモに紐付けて中間テーブルに保存
                MemoService::attachExistingBaits($request, $memo->id);
                // 釣果データ（名前・匹数・長さ）を、メモに紐付けて中間テーブルに保存
                MemoService::attachExistingFishNames($request, $memo->id);
                // 新規タグの入力があれば、各データを保存。
                TagService::createNewTag($request->new_tag, $memo->id);
                // 既存のタグの選択があれば、メモに紐付けて中間テーブルに保存
                MemoService::attachExistingTags($request, $memo->id);
                // 既存の画像の選択があれば、メモに紐付けて中間テーブルに保存
                MemoService::attachExistingImages($request, $memo->id);
            }, 10);

            return to_route('user.index')->with(['message' => 'メモを登録しました。', 'status' => 'success']);
        } catch (Throwable $e) {
            Log::error($e);
            return back()->with(['message' => 'メモの登録に失敗しました。', 'status' => 'error']);
        }
    }

    /**
     *  メモの詳細を表示するメソッド。
     * @param int $id
     * @return View
     */
    public function show(int $id): View
    {
        // 選択したメモを、一件取得
        $select_memo = Memo::availableSelectMemo($id)->first();
        // 選択したメモに紐づいた釣り場の名前を取得
        $get_memo_spots_name = SpotService::getMemoSpotsResults($select_memo->spots);
        // 選択したメモに紐づいたエサの名前を取得
        $get_memo_baits_name = BaitService::getMemoBaitsName($select_memo->baits);
        // 選択したメモに紐づいた釣果のデータを取得（名前・匹数・長さ）
        $get_memo_fish_results = FishNameService::getMemoFishResults($select_memo->fish_names);
        // 選択したメモに紐づいたタグの名前を取得
        $get_memo_tags_name = TagService::getMemoTagsName($select_memo->tags);
        // 選択したメモに紐づいた画像を取得
        $get_memo_images = ImageService::getMemoImages($select_memo->images);
        // 共有されているメモに目印を付ける
        MemoService::checkShared($select_memo);
        // 自分が共有しているメモの、共有状態の情報を取得
        $shared_users = ShareSettingService::checkSharedMemoStatus($id);

        return view('user.memos.show', compact('select_memo', 'get_memo_spots_name', 'get_memo_baits_name', 'get_memo_tags_name', 'get_memo_images', 'shared_users', 'get_memo_fish_results'));
    }

    /**
     * メモの編集画面を表示するメソッド。
     * @param int $id
     * @return View
     */
    public function edit(int $id): View
    {
        // 全スポットを取得する
        $all_spots = Spot::availableAllSpots()->get();
        // 全エサを取得する
        $all_baits = Bait::availableAllBaits()->get();
        // 全魚名を取得する
        $all_fish_names = FishName::availableAllFishNames()->get();
        // 全タグの一覧表示
        $all_tags = Tag::availableAllTags()->get();
        // 全画像を取得する
        $all_images = Image::availableAllImages()->get();
        // 選択したメモを、一件取得。
        $select_memo = Memo::availableSelectMemo($id)->first();
        // 選択したメモに紐づいたタグのidを取得
        $get_memo_tags_id = TagService::getMemoTagsId($select_memo->tags);
        // 選択したメモに紐づいた画像を取得
        $get_memo_images = ImageService::getMemoImages($select_memo->images);
        // 選択したメモに紐づいた画像のidを取得
        $get_memo_images_id = ImageService::getMemoImagesId($select_memo->images);
        // 共有されているメモに目印を付ける
        MemoService::checkShared($select_memo);
        // ブラウザバック対策（値を持たせる）
        SessionService::setBrowserBackSession();

        return view(
            'user.memos.edit',
            compact('all_spots', 'all_baits', 'all_fish_names', 'all_tags', 'all_images', 'select_memo', 'get_memo_tags_id', 'get_memo_images_id', 'get_memo_images')
        );
    }

    /**
     * メモを更新するメソッド。
     * @param StoreMemoRequest $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function update(StoreMemoRequest $request): RedirectResponse
    {
        // ブラウザバック対策（値を確認）
        SessionService::clickBrowserBackSession();
        try {
            DB::transaction(function () use ($request) {
                // メモを更新
                $memo = MemoService::updateMemo($request);
                // 一旦メモと釣り場を紐付けた中間デーブルのデータを削除
                MemoSpot::where('memo_id', $request->memoId)->delete();
                // 一旦メモとエサを紐付けた中間デーブルのデータを削除
                MemoBait::where('memo_id', $request->memoId)->delete();
                // 一旦メモと釣果のデータを紐付けた中間デーブルのデータを削除
                MemoFishName::where('memo_id', $request->memoId)->delete();
                // 一旦メモとタグを紐付けた中間デーブルのデータを削除
                MemoTag::where('memo_id', $request->memoId)->delete();
                // 一旦メモと画像を紐付けた中間デーブルのデータを削除
                MemoImage::where('memo_id', $request->memoId)->delete();
                // 釣り場を、メモに紐付けて中間テーブルに保存
                MemoService::attachExistingSpots($request, $memo->id);
                // エサを、メモに紐付けて中間テーブルに保存
                MemoService::attachExistingBaits($request, $memo->id);
                // 釣果データ（名前・匹数・長さ）を、メモに紐付けて中間テーブルに保存
                MemoService::attachExistingFishNames($request, $memo->id);
                // 新規タグの入力があれば、各データを保存。
                TagService::createNewTag($request->new_tag, $memo->id);
                // 既存のタグの選択があれば、メモに紐付けて中間テーブルに保存
                MemoService::attachExistingTags($request, $memo->id);
                // 既存の画像の選択があれば、メモに紐付けて中間テーブルに保存
                MemoService::attachExistingImages($request, $memo->id);
            }, 10);

            return to_route('user.index')->with(['message' => 'メモを更新しました。', 'status' => 'success']);
        } catch (Throwable $e) {
            Log::error($e);
            return back()->with(['message' => 'メモの更新に失敗しました。', 'status' => 'error']);
        }
    }

    /**
     * メモを削除（ソフトデリート）するメソッド。
     * @param Request $request
     * @return RedirectResponse
     * @throws Throwable
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                // 選択したメモの全ての共有設定を解除
                ShareSettingService::deleteShareSettingAll($request->memoId);
                // 選択したメモを削除
                Memo::availableSelectMemo($request->memoId)->delete();
            }, 10);

            return to_route('user.index')->with(['message' => 'メモをゴミ箱に移動しました。', 'status' => 'success']);
        } catch (Throwable $e) {
            Log::error($e);
            return back()->with(['message' => 'メモの削除に失敗しました。', 'status' => 'error']);
        }
    }
}
