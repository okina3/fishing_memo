<?php

namespace App\Services;

use App\Models\Spot;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class SpotService
{
   /**
    * 別のユーザーの釣り場を見られなくする為のメソッド。
    * @param $request
    * @return void
    */
   public static function checkUserSpot($request): void
   {
      // パラメーターを取得
      $id_spot = $request->route()->parameter('spot');
      // パラメーターが無ければチェック不要
      if (!is_null($id_spot)) {
         // 自分自身の釣り場なのかチェック
         $spot = Spot::select('user_id')->findOrFail($id_spot);
         if ($spot->user_id !== Auth::id()) {
            abort(404);
         }
      }
   }

   /**
    * 新しい釣り場を保存するメソッド。
    * @param string $new_spot
    * @return Spot
    */
   public static function createSpot(string $new_spot): Spot
   {
      return Spot::create([
         'name' => $new_spot,
         'user_id' => Auth::id(),
      ]);
   }

   /**
    * 既存の釣り場を更新するメソッド。
    * @param int $spotId
    * @param string $spot_name
    * @return Spot
    */
   public static function updateSpot(int $spotId, string $spot_name): Spot
   {
      $spot = Spot::availableSelectSpot($spotId)->firstOrFail();
      $spot->name = $spot_name;
      $spot->save();

      return $spot;
   }

   /**
    * 選択したメモに紐づいた、場所のデータ（釣り場、流れ、濁り、水位、水温）を、配列で取得するメソッド。
    * @param Collection $select_memo_spots
    * @return array
    */
   public static function getMemoSpotsResults(Collection $select_memo_spots): array
   {
      return $select_memo_spots->map(function ($spot) {
         return [
            'name' => $spot->name,
            'river_flow' => $spot->pivot->river_flow ?? null,
            'turbidity' => $spot->pivot->turbidity ?? null,
            'water_level' => $spot->pivot->water_level ?? null,
            'water_temp' => $spot->pivot->water_temp ?? null,
         ];
      })->toArray();
   }
}
