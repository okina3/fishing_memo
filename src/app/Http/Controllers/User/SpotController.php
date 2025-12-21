<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\StoreSpotRequest;
use App\Models\Spot;
use App\Services\SessionService;
use App\Services\SpotService;
use Closure;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class SpotController extends Controller
{
    public function __construct()
    {
        // 別のユーザーの釣り場を見られなくする認証。
        $this->middleware(function (Request $request, Closure $next) {
            SpotService::checkUserSpot($request);
            return $next($request);
        });
    }

    /**
     * 釣り場を保存するメソッド。
     * @param StoreSpotRequest $request
     * @return RedirectResponse
     */
    public function store(StoreSpotRequest $request): RedirectResponse
    {
        try {
            SpotService::createSpot($request->input('spot_name'));

            return to_route('user.masters.index', ['tab' => 'spots'])
                ->with(['message' => '釣り場を追加しました。', 'status' => 'success']);
        } catch (Throwable $e) {
            Log::error($e);
            return back()->with(['message' => '釣り場の追加に失敗しました', 'status' => 'error']);
        }
    }

    /**
     * Ajaxで、釣り場を保存するメソッド。
     * @param StoreSpotRequest $request
     * @return JsonResponse
     * @throws Throwable
     */
    public function storeAjax(StoreSpotRequest $request): JsonResponse
    {
        try {
            $spot = SpotService::createSpot($request->input('spot_name'));

            return response()->json([
                'id' => $spot->id,
                'name' => $spot->name,
            ], 201);
        } catch (Throwable $e) {
            Log::error($e);
            return response()->json([
                'message' => '釣り場の登録に失敗しました。',
                'status' => 'error'
            ], 500);
        }
    }

    /**
     * 釣り場の編集画面を表示するメソッド。
     * @param int $id
     * @return View
     */
    public function edit(int $id): View
    {
        // 選択した釣り場を、一件取得。
        $spot = Spot::availableSelectSpot($id)->firstOrFail();
        // ブラウザバック対策（値を持たせる）
        SessionService::setBrowserBackSession();

        return view('user.masters.spots.edit-spot', compact('spot'));
    }

    /**
     * 釣り場名を更新するメソッド。
     * @param StoreSpotRequest $request
     * @return RedirectResponse
     */
    public function update(StoreSpotRequest $request): RedirectResponse
    {
        try {
            // 釣り場を更新
            SpotService::updateSpot((int) $request->spotId, (string) $request->input('spot_name'));

            return to_route('user.masters.index', ['tab' => 'spots'])
                ->with(['message' => '釣り場名を更新しました。', 'status' => 'success']);
        } catch (Throwable $e) {
            Log::error($e);
            return back()->with(['message' => '釣り場名の更新に失敗しました。', 'status' => 'error']);
        }
    }

    /**
     * 釣り場を削除するメソッド。
     * @param Request $request
     * @return RedirectResponse
     */
    public function destroy(Request $request): RedirectResponse
    {
        try {
            // 指定の釣り場を取得
            $spot = Spot::availableSelectSpot($request->spotId)->first();
            // 関連がある場合は削除不可
            if ($spot->memos()->exists()) {
                return redirect()->back()->with(['message' => '関連データのため削除できません。', 'status' => 'error']);
            }
            // 選択した釣り場を削除
            $spot->delete();
            return redirect()->back()->with(['message' => '正常に釣り場を削除しました。', 'status' => 'success']);
        } catch (Throwable $e) {
            Log::error($e);
            return redirect()->back()->with(['message' => '釣り場の削除に失敗しました。', 'status' => 'error']);
        }
    }
}
