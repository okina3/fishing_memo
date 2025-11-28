<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\DeleteTagRequest;
use App\Http\Requests\User\StoreTagRequest;
use App\Models\Tag;
use App\Services\SessionService;
use App\Services\TagService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class TagController extends Controller
{
    /**
     * タグの一覧を表示するメソッド。
     * @return View
     */
    public function index(): View
    {
        // ブラウザバック対策（値を削除する）
        SessionService::resetBrowserBackSession();
        // タグを取得する
        $all_tags = Tag::availableAllTags()->get();

        return view('user.tags.index', compact('all_tags'));
    }

    /**
     * タグを保存するメソッド。
     * @param StoreTagRequest $request
     * @return RedirectResponse
     */
    public function store(StoreTagRequest $request): RedirectResponse
    {
        try {
            TagService::createTag($request->new_tag);

            return to_route('user.tag.index')->with(['message' => 'タグを登録しました。', 'status' => 'success']);
        } catch (Throwable $e) {
            Log::error($e);
            return back()->with(['message' => 'タグの登録に失敗しました。', 'status' => 'error']);
        }
    }

    /**
     * タグを削除するメソッド。
     * @param DeleteTagRequest $request
     * @return RedirectResponse
     */
    public function destroy(DeleteTagRequest $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                // タグを複数まとめて削除
                TagService::deleteTags((array) $request->tags);
            }, 10);

            return to_route('user.tag.index')->with(['message' => '正常にタグを削除しました。', 'status' => 'success']);
        } catch (Throwable $e) {
            Log::error($e);
            return back()->with(['message' => 'タグの削除に失敗しました。', 'status' => 'error']);
        }
    }
}
