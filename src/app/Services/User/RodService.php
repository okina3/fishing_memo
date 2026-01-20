<?php

namespace App\Services\User;

use App\Models\Rod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class RodService
{
   /**
    * 別のユーザーの釣り竿を見られなくする為のメソッド。
    * @param $request
    * @return void
    */
   public static function checkUserRod($request): void
   {
      // パラメーターを取得
      $id_rod = $request->route()->parameter('rod');
      // パラメーターが無ければチェック不要
      if (!is_null($id_rod)) {
         // 自分自身の釣り竿なのかチェック
         $rod = Rod::select('user_id')->findOrFail($id_rod);
         if ($rod->user_id !== Auth::id()) {
            abort(404);
         }
      }
   }

   /**
    * 新しい釣り竿を保存するメソッド。
    * @param string $name
    * @return Rod
    */
   public static function createRod(string $name): Rod
   {
      return Rod::create([
         'name' => $name,
         'user_id' => Auth::id(),
      ]);
   }

   /**
    * 既存の釣り竿を更新するメソッド。
    * @param int $rodId
    * @param string $name
    * @return Rod
    */
   public static function updateRod(int $rodId, string $name): Rod
   {
      $rod = Rod::availableSelectRod($rodId)->firstOrFail();
      $rod->name = $name;
      $rod->save();

      return $rod;
   }

   /**
    * 選択したメモに紐づいた釣り竿のデータ（釣り竿・道糸）を配列で取得するメソッド。
    * @param Collection $select_memo_rods
    * @return array
    */
   public static function getMemoRodsResults(Collection $select_memo_rods): array
   {
      return $select_memo_rods->map(function ($rod) {
         return [
            'name' => $rod->name,
            'main_line' => $rod->pivot->main_line ?? null,
         ];
      })->toArray();
   }
}
