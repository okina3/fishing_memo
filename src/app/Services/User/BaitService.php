<?php

namespace App\Services\User;

use App\Models\Bait;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class BaitService
{
   /**
    * 別のユーザーのエサを見られなくする為のメソッド。
    * @param $request
    * @return void
    */
   public static function checkUserBait($request): void
   {
      // パラメーターを取得
      $id_bait = $request->route()->parameter('bait');
      // パラメーターが無ければチェック不要
      if (!is_null($id_bait)) {
         // 自分自身のエサなのかチェック
         $bait = Bait::select('user_id')->findOrFail($id_bait);
         if ($bait->user_id !== Auth::id()) {
            abort(404);
         }
      }
   }

   /**
    * 新しいエサを保存するメソッド。
    * @param string $new_bait
    * @return Bait
    */
   public static function createBait(string $new_bait): Bait
   {
      return Bait::create([
         'name' => $new_bait,
         'user_id' => Auth::id(),
      ]);
   }

   /**
    * 既存のエサ名を更新するメソッド。
    * @param int $baitId
    * @param string $bait_name
    * @return Bait
    */
   public static function updateBait(int $baitId, string $bait_name): Bait
   {
      $bait = Bait::availableSelectBait($baitId)->firstOrFail();
      $bait->name = $bait_name;
      $bait->save();

      return $bait;
   }

   /**
    * 選択したメモに紐づいた、エサのNameを、配列で取得するメソッド。
    * @param Collection $select_memo_baits
    * @return array
    */
   public static function getMemoBaitsName(Collection $select_memo_baits): array
   {
      return $select_memo_baits->pluck('name')->toArray();
   }
}
