<?php

namespace App\Services;

use App\Models\FishName;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class FishNameService
{
   /**
    * 別のユーザーの魚名を見られなくする為のメソッド。
    * @param $request
    * @return void
    */
   public static function checkUserFishName($request): void
   {
      // パラメーターを取得
      $id_fish_name = $request->route()->parameter('fishName');
      // パラメーターが無ければチェック不要
      if (!is_null($id_fish_name)) {
         // 自分自身の魚名なのかチェック
         $fish_name = FishName::select('user_id')->findOrFail($id_fish_name);
         if ($fish_name->user_id !== Auth::id()) {
            abort(404);
         }
      }
   }

   /**
    * 新しい魚名を保存するメソッド。
    * @param string $new_fish_name
    * @return FishName
    */
   public static function createFishName(string $new_fish_name): FishName
   {
      return FishName::create([
         'name' => $new_fish_name,
         'user_id' => Auth::id(),
      ]);
   }

   /**
    * 既存の魚名を更新するメソッド。
    * @param int $fishNameId
    * @param string $fish_name
    * @return FishName
    */
   public static function updateFishName(int $fishNameId, string $fish_name): FishName
   {
      $fish = FishName::availableSelectFishName($fishNameId)->firstOrFail();
      $fish->name = $fish_name;
      $fish->save();

      return $fish;
   }

   /**
    * 選択したメモに紐づいた釣果のデータ（名前・匹数・長さ）を配列で取得するメソッド。
    * @param Collection $select_memo_fish_names
    * @return array
    */
   public static function getMemoFishResults(Collection $select_memo_fish_names): array
   {
      return $select_memo_fish_names->map(function ($fish) {
         return [
            'name' => $fish->name,
            'count' => $fish->pivot->count ?? 0,
            'length' => $fish->pivot->length ?? 0,
         ];
      })->toArray();
   }
}
