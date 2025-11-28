<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Memo;
use App\Models\MemoBait;
use App\Models\MemoFishName;
use App\Models\MemoImage;
use App\Models\MemoTag;
use App\Services\SessionService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class TrashedMemoController extends Controller
{
    /**
     * ソフトデリートしたメモ一覧を表示するメソッド。
     * @return View
     */
    public function index(): View
    {
        // ブラウザバック対策（値を削除する）
        SessionService::resetBrowserBackSession();

        $all_trashed_memos = Memo::availableAllTrashedMemos()->get();

        return view('user.trashedMemos.index', compact('all_trashed_memos'));
    }

    /**
     * ソフトデリートしたメモを元に戻すメソッド。
     * @param Request $request
     * @return RedirectResponse
     */
    public function undo(Request $request): RedirectResponse
    {
        Memo::availableSelectTrashedMemo($request->memoId)->restore();

        return to_route('user.trashed-memo.index')->with(['message' => 'メモを元に戻しました。', 'status' => 'success']);
    }

    /**
     * ソフトデリートしたメモを完全削除するメソッド。
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {
            DB::transaction(function () use ($request) {
                // メモを完全削除
                Memo::availableSelectTrashedMemo($request->memoId)->forceDelete();
                // 中間テーブルのデータを削除
                MemoBait::where('memo_id', $request->memoId)->delete();
                MemoFishName::where('memo_id', $request->memoId)->delete();
                MemoTag::where('memo_id', $request->memoId)->delete();
                MemoImage::where('memo_id', $request->memoId)->delete();
            }, 10);

            return to_route('user.trashed-memo.index')->with(['message' => 'メモを完全に削除しました。', 'status' => 'success']);
        } catch (Throwable $e) {
            Log::error($e);
            return back()->with(['message' => 'メモの完全削除に失敗しました。', 'status' => 'error']);
        }
    }
}
