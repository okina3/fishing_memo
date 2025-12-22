<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Memo;
use App\Services\SessionService;
use App\Services\User\TrashedMemoService;
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

      $perPage = 10;
      $keyword = request()->query('keyword');
      $all_trashed_memos = Memo::availableAllTrashedMemos()
         ->searchKeyword($keyword)
         ->paginate($perPage)
         ->appends(request()->query());

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
    * @throws Throwable
    */
   public function destroy(Request $request): RedirectResponse
   {
      try {
         DB::transaction(function () use ($request) {
            // 中間テーブルのデータを削除
            TrashedMemoService::deleteRelatedRecords((int) $request->memoId);
            // メモを完全削除
            Memo::availableSelectTrashedMemo($request->memoId)->forceDelete();
         }, 10);

         return to_route('user.trashed-memo.index')->with(['message' => 'メモを完全に削除しました。', 'status' => 'success']);
      } catch (Throwable $e) {
         Log::error($e);
         return back()->with(['message' => 'メモの完全削除に失敗しました。', 'status' => 'error']);
      }
   }
}
