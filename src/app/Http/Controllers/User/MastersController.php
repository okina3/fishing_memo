<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\SearchKeywordRequest;
use App\Models\Bait;
use App\Models\FishName;
use App\Models\Hook;
use App\Models\Rod;
use App\Models\Spot;
use App\Services\SessionService;
use Illuminate\View\View;

class MastersController extends Controller
{
   /**
    * 釣り場/釣り竿/エサ/魚名（マスターズ管理画面）を一覧表示するメソッド。
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
      $rods = Rod::with('user')
         ->searchKeyword($request->keyword)->availableAllRods()->get();
      $hooks = Hook::with('user')
         ->searchKeyword($request->keyword)->availableAllHooks()->get();
      $baits = Bait::with('user')
         ->searchKeyword($request->keyword)->availableAllBaits()->get();
      $fishNames = FishName::with('user')
         ->searchKeyword($request->keyword)->availableAllFishNames()->get();
      // 検索後に入力欄がクリア
      $keyword = '';

      return view('user.masters.index', compact('tab', 'spots', 'rods', 'hooks', 'baits', 'fishNames', 'keyword'));
   }
}
