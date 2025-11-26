<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreBaitRequest;
use App\Models\Bait;
use App\Services\BaitService;
use App\Services\SessionService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class BaitController extends Controller
{
   public function __construct()
   {
      // 別のユーザーのエサを見られなくする認証。
      $this->middleware(function (Request $request, Closure $next) {
         BaitService::checkUserBait($request);
         return $next($request);
      });
   }

   /**
    * 新規メモ作成から新規エサを保存するメソッド。
    * @param StoreBaitRequest $request
    * @return JsonResponse
    * @throws Throwable
    */
   public function store(StoreBaitRequest $request): JsonResponse
   {
      try {
         $bait = BaitService::createBait($request->input('bait_name'));

         return response()->json([
            'id' => $bait->id,
            'name' => $bait->name,
         ], 201);
      } catch (Throwable $e) {
         Log::error($e);
         return response()->json([
            'message' => 'エサの登録に失敗しました。',
            'status' => 'alert'
         ], 500);
      }
   }

   /**
    * エサの編集画面を表示するメソッド。
    * @param int $id
    * @return View
    */
   public function edit(int $id): View
   {
      // 選択したエサを、一件取得。
      $bait = Bait::availableSelectBait($id)->firstOrFail();
      // ブラウザバック対策（値を持たせる）
      SessionService::setBrowserBackSession();

      return view('user.masters.edit-bait', compact('bait'));
   }

   /**
    * エサ名を更新するメソッド。
    * @param StoreBaitRequest $request
    * @return RedirectResponse
    */
   public function update(StoreBaitRequest $request): RedirectResponse
   {
      try {
         // エサを更新
         BaitService::updateBait((int) $request->baitId, (string) $request->input('bait_name'));
         
         return to_route('user.masters.index', ['tab' => 'baits'])
            ->with(['message' => 'エサ名を更新しました。', 'status' => 'info']);
      } catch (Throwable $e) {
         Log::error($e);
         return back()->with(['message' => 'エサ名の更新に失敗しました。', 'status' => 'alert']);
      }
   }

   /**
    * エサを削除するメソッド。
    * @param Request $request
    * @return RedirectResponse
    */
   public function destroy(Request $request): RedirectResponse
   {
      try {
         // 指定のエサを取得
         $bait = Bait::availableSelectBait($request->baitId)->first();
         // 関連がある場合は削除不可
         if ($bait->memos()->exists()) {
            return redirect()->back()->with(['message' => '関連データのため削除できません。', 'status' => 'alert']);
         }
         // 選択したエサを削除
         $bait->delete();
         return redirect()->back()->with(['message' => '正常にエサを削除しました。', 'status' => 'info']);
      } catch (Throwable $e) {
         Log::error($e);
         return redirect()->back()->with(['message' => 'エサの削除に失敗しました。', 'status' => 'alert']);
      }
   }
}
