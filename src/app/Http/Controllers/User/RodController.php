<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreRodRequest;
use App\Models\Rod;
use App\Services\SessionService;
use App\Services\User\RodService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class RodController extends Controller
{
   public function __construct()
   {
      // 別のユーザーのロッドを見られなくする認証。
      $this->middleware(function (Request $request, Closure $next) {
         RodService::checkUserRod($request);
         return $next($request);
      });
   }

   /**
    * 釣り竿を保存するメソッド。
    * @param StoreRodRequest $request
    * @return RedirectResponse
    */
   public function store(StoreRodRequest $request): RedirectResponse
   {
      try {
         RodService::createRod((string) $request->input('rod_name'));

         return to_route('user.masters.index', ['tab' => 'rods'])
            ->with(['message' => '釣り竿を追加しました。', 'status' => 'success']);
      } catch (Throwable $e) {
         Log::error($e);
         return back()->with(['message' => '釣り竿の追加に失敗しました', 'status' => 'error']);
      }
   }

   /**
    * Ajaxで、釣り竿を保存するメソッド。
    * @param StoreRodRequest $request
    * @return JsonResponse
    * @throws Throwable
    */
   public function storeAjax(StoreRodRequest $request): JsonResponse
   {
      try {
         $rod = RodService::createRod((string) $request->input('rod_name'));

         return response()->json([
            'id' => $rod->id,
            'name' => $rod->name,
         ], 201);
      } catch (Throwable $e) {
         Log::error($e);
         return response()->json([
            'message' => '釣り竿の登録に失敗しました。',
            'status' => 'error'
         ], 500);
      }
   }

   /**
    * 釣り竿の編集画面を表示するメソッド。
    * @param int $id
    * @return View
    */
   public function edit(int $id): View
   {
      // 選択した釣り竿を、一件取得。
      $rod = Rod::availableSelectRod($id)->firstOrFail();
      // ブラウザバック対策（値を持たせる）
      SessionService::setBrowserBackSession();

      return view('user.masters.partials.rods.edit-rod', compact('rod'));
   }

   /**
    * 釣り竿名を更新するメソッド。
    * @param StoreRodRequest $request
    * @return RedirectResponse
    */
   public function update(StoreRodRequest $request): RedirectResponse
   {
      try {
         // 釣り竿名を更新
         RodService::updateRod((int) $request->rodId, (string) $request->input('rod_name'));

         return to_route('user.masters.index', ['tab' => 'rods'])
            ->with(['message' => '釣り竿名を更新しました。', 'status' => 'success']);
      } catch (Throwable $e) {
         Log::error($e);
         return back()->with(['message' => '釣り竿名の更新に失敗しました。', 'status' => 'error']);
      }
   }

   /**
    * 釣り竿を削除するメソッド。
    * @param Request $request
    * @return RedirectResponse
    */
   public function destroy(Request $request): RedirectResponse
   {
      try {
         // 指定の釣り竿を取得
         $rod = Rod::availableSelectRod($request->rodId)->first();
         // 関連がある場合は削除不可
         if ($rod->memos()->exists()) {
            return redirect()->back()->with(['message' => '関連データのため削除できません。', 'status' => 'error']);
         }
         // 選択した釣り竿を削除
         $rod->delete();
         return redirect()->back()->with(['message' => '正常に釣り竿を削除しました。', 'status' => 'success']);
      } catch (Throwable $e) {
         Log::error($e);
         return redirect()->back()->with(['message' => '釣り竿の削除に失敗しました。', 'status' => 'error']);
      }
   }
}
