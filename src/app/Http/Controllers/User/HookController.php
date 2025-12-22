<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreHookRequest;
use App\Models\Hook;
use App\Services\SessionService;
use App\Services\User\HookService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class HookController extends Controller
{
   public function __construct()
   {
      // 別のユーザーの釣り針を見られなくする認証。
      $this->middleware(function (Request $request, Closure $next) {
         HookService::checkUserHook($request);
         return $next($request);
      });
   }

   /**
    * 釣り針を保存するメソッド。
    * @param StoreHookRequest $request
    * @return RedirectResponse
    */
   public function store(StoreHookRequest $request): RedirectResponse
   {
      try {
         HookService::createHook((string) $request->input('hook_name'));

         return to_route('user.masters.index', ['tab' => 'hooks'])
            ->with(['message' => '釣り針を追加しました。', 'status' => 'success']);
      } catch (Throwable $e) {
         Log::error($e);
         return back()->with(['message' => '釣り針の追加に失敗しました', 'status' => 'error']);
      }
   }

   /**
    * Ajaxで、釣り針を保存するメソッド。
    * @param StoreHookRequest $request
    * @return JsonResponse
    * @throws Throwable
    */
   public function storeAjax(StoreHookRequest $request): JsonResponse
   {
      try {
         $hook = HookService::createHook((string) $request->input('hook_name'));

         return response()->json([
            'id' => $hook->id,
            'name' => $hook->name,
         ], 201);
      } catch (Throwable $e) {
         Log::error($e);
         return response()->json([
            'message' => '釣り針の登録に失敗しました。',
            'status' => 'error'
         ], 500);
      }
   }

   /**
    * 釣り針の編集画面を表示するメソッド。
    * @param int $id
    * @return View
    */
   public function edit(int $id): View
   {
      // 選択した釣り針を、一件取得。
      $hook = Hook::availableSelectHook($id)->firstOrFail();
      // ブラウザバック対策（値を持たせる）
      SessionService::setBrowserBackSession();

      return view('user.masters.partials.hooks.edit-hook', compact('hook'));
   }

   /**
    * 釣り針名を更新するメソッド。
    * @param StoreHookRequest $request
    * @return RedirectResponse
    */
   public function update(StoreHookRequest $request): RedirectResponse
   {
      try {
         // 釣り針名を更新
         HookService::updateHook((int) $request->hookId, (string) $request->input('hook_name'));

         return to_route('user.masters.index', ['tab' => 'hooks'])
            ->with(['message' => '釣り針名を更新しました。', 'status' => 'success']);
      } catch (Throwable $e) {
         Log::error($e);
         return back()->with(['message' => '釣り針名の更新に失敗しました。', 'status' => 'error']);
      }
   }

   /**
    * 釣り針を削除するメソッド。
    * @param Request $request
    * @return RedirectResponse
    */
   public function destroy(Request $request): RedirectResponse
   {
      try {
         // 指定の釣り針を取得
         $hook = Hook::availableSelectHook($request->hookId)->first();
         // 関連がある場合は削除不可
         if ($hook->memos()->exists()) {
            return redirect()->back()->with(['message' => '関連データのため削除できません。', 'status' => 'error']);
         }
         // 選択した釣り針を削除
         $hook->delete();
         return redirect()->back()->with(['message' => '正常に釣り針を削除しました。', 'status' => 'success']);
      } catch (Throwable $e) {
         Log::error($e);
         return redirect()->back()->with(['message' => '釣り針の削除に失敗しました。', 'status' => 'error']);
      }
   }
}
