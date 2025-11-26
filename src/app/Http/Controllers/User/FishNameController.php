<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreFishRequest;
use App\Models\FishName;
use App\Services\FishNameService;
use App\Services\SessionService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class FishNameController extends Controller
{
   public function __construct()
   {
      // 別のユーザーの魚名を見られなくする認証。
      $this->middleware(function (Request $request, Closure $next) {
         FishNameService::checkUserFishName($request);
         return $next($request);
      });
   }

   /**
    * 新規メモ作成から新規魚名を保存するメソッド
    * @param StoreFishRequest $request
    * @return JsonResponse
    * @throws Throwable
    */
   public function store(StoreFishRequest $request): JsonResponse
   {
      try {
         $fish = FishNameService::createFishName($request->input('fish_name'));

         return response()->json([
            'id' => $fish->id,
            'name' => $fish->name,
         ], 201);
      } catch (Throwable $e) {
         Log::error($e);
         return response()->json([
            'message' => '魚名の登録に失敗しました。',
            'status' => 'alert'
         ], 500);
      }
   }

   /**
    * 魚名の編集画面を表示するメソッド。
    * @param int $id
    * @return View
    */
   public function edit(int $id): View
   {
      // 選択した魚名を、一件取得。
      $fish_name = FishName::availableSelectFishName($id)->firstOrFail();
      // ブラウザバック対策（値を持たせる）
      SessionService::setBrowserBackSession();

      return view('user.masters.edit-fish-name', compact('fish_name'));
   }

   /**
    * 魚名を更新するメソッド。
    * @param StoreFishRequest $request
    * @return RedirectResponse
    */
   public function update(StoreFishRequest $request): RedirectResponse
   {
      try {
         // 魚名を更新
         FishNameService::updateFishName((int) $request->fishNameId, (string) $request->input('fish_name'));

         return to_route('user.masters.index', ['tab' => 'fishNames'])
            ->with(['message' => '魚名を更新しました。', 'status' => 'info']);
      } catch (Throwable $e) {
         Log::error($e);
         return back()->with(['message' => '魚名の更新に失敗しました。', 'status' => 'alert']);
      }
   }

   /**
    * 魚名を削除するメソッド。
    * @param Request $request
    * @return RedirectResponse
    */
   public function destroy(Request $request): RedirectResponse
   {
      try {
         // 指定の魚名を取得
         $fish_name = FishName::availableSelectFishName($request->fishNameId)->first();
         // 関連がある場合は削除不可
         if ($fish_name->memos()->exists()) {
            return redirect()->back()->with(['message' => '関連データのため削除できません。', 'status' => 'alert']);
         }
         // 選択した魚名を削除
         $fish_name->delete();
         return redirect()->back()->with(['message' => '正常に魚名を削除しました。', 'status' => 'info']);
      } catch (Throwable $e) {
         Log::error($e);
         return redirect()->back()->with(['message' => '魚名の削除に失敗しました。', 'status' => 'alert']);
      }
   }
}
