<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\SearchKeywordRequest;
use App\Http\Requests\User\StoreBaitRequest;
use App\Http\Requests\User\StoreFishRequest;
use App\Http\Requests\User\StoreSpotRequest;
use App\Models\Bait;
use App\Models\FishName;
use App\Models\Spot;
use App\Services\BaitService;
use App\Services\FishNameService;
use App\Services\SessionService;
use App\Services\SpotService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

class MastersController extends Controller
{
   /**
    * スポット/エサ/魚名（マスターズ管理画面）を一覧表示するメソッド。
    * @param SearchKeywordRequest $request
    * @return View
    */
   public function index(SearchKeywordRequest $request): View
   {
      // ブラウザバック対策（値を削除する）
      SessionService::resetBrowserBackSession();

      // 表示するタブを取得
      $tab = $request->get('tab', 'spots');
      // 各マスターズデータを検索する
      $spots = Spot::with('user')
         ->searchKeyword($request->keyword)->availableAllSpots()->get();
      $baits = Bait::with('user')
         ->searchKeyword($request->keyword)->availableAllBaits()->get();
      $fishNames = FishName::with('user')
         ->searchKeyword($request->keyword)->availableAllFishNames()->get();
      // 検索後に入力欄がクリア
      $keyword = '';

      return view('user.masters.index', compact('tab', 'spots', 'baits', 'fishNames', 'keyword'));
   }

   /**
    * マスターズ管理画面から釣り場を保存するメソッド。
    * @param StoreSpotRequest $request
    * @return RedirectResponse
    */
   public function storeSpot(StoreSpotRequest $request): RedirectResponse
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
    * マスターズ管理画面からエサを保存するメソッド。
    * @param StoreBaitRequest $request
    * @return RedirectResponse
    */
   public function storeBait(StoreBaitRequest $request): RedirectResponse
   {
      try {
         BaitService::createBait($request->input('bait_name'));

         return to_route('user.masters.index', ['tab' => 'baits'])
            ->with(['message' => 'エサを追加しました。', 'status' => 'success']);
      } catch (Throwable $e) {
         Log::error($e);
         return back()->with(['message' => 'エサの追加に失敗しました', 'status' => 'error']);
      }
   }

   /**
    * マスターズ管理画面から魚名を保存するメソッド。
    * @param StoreFishRequest $request
    * @return RedirectResponse
    */
   public function storeFishName(StoreFishRequest $request): RedirectResponse
   {
      try {
         FishNameService::createFishName($request->input('fish_name'));

         return to_route('user.masters.index', ['tab' => 'fishNames'])
            ->with(['message' => '魚名を追加しました。', 'status' => 'success']);
      } catch (Throwable $e) {
         Log::error($e);
         return back()->with(['message' => '魚名の追加に失敗しました', 'status' => 'error']);
      }
   }
}
