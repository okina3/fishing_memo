<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\DeleteTagRequest;
use App\Http\Requests\User\StoreTagRequest;
use App\Models\Tag;
use App\Services\SessionService;
use App\Services\TagService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

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
        //タグを保存
        TagService::createTag($request->new_tag);

        return to_route('user.tag.index')->with(['message' => 'タグを登録しました。', 'status' => 'info']);
    }

    /**
     * タグを削除するメソッド。
     * @param DeleteTagRequest $request
     * @return RedirectResponse
     */
    public function destroy(DeleteTagRequest $request): RedirectResponse
    {
        // タグを複数まとめて削除
        TagService::deleteTags((array) $request->tags);

        return to_route('user.tag.index')->with(['message' => '正常にタグを削除しました。', 'status' => 'info']);
    }
}
