<?php

namespace App\Services\User;

use App\Models\Hook;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class HookService
{
   /**
    * 別のユーザーの釣り針を見られなくする為のメソッド。
    * @param $request
    * @return void
    */
   public static function checkUserHook($request): void
   {
      // パラメーターを取得
      $id_hook = $request->route()->parameter('hook');
      // パラメーターが無ければチェック不要
      if (!is_null($id_hook)) {
         // 自分自身の釣り針なのかチェック
         $hook = Hook::select('user_id')->findOrFail($id_hook);
         if ($hook->user_id !== Auth::id()) {
            abort(404);
         }
      }
   }

   /**
    * 新しい釣り針を保存するメソッド。
    * @param string $name
    * @return Hook
    */
   public static function createHook(string $name): Hook
   {
      return Hook::create([
         'name' => $name,
         'user_id' => Auth::id(),
      ]);
   }

   /**
    * 既存の釣り針を更新するメソッド。
    * @param int $hookId
    * @param string $name
    * @return Hook
    */
   public static function updateHook(int $hookId, string $name): Hook
   {
      $hook = Hook::availableSelectHook($hookId)->firstOrFail();
      $hook->name = $name;
      $hook->save();

      return $hook;
   }

   /**
    * 選択したメモに紐づいた釣り針のデータ（釣り針・ハリス情報）を配列で取得するメソッド。
    * @param Collection $select_memo_hooks
    * @return array
    */
   public static function getMemoHooksResults(Collection $select_memo_hooks): array
   {
      return $select_memo_hooks->map(function ($hook) {
         return [
            'name' => $hook->name,
            'leader_size' => $hook->pivot->leader_size ?? null,
            'leader_upper_cm' => $hook->pivot->leader_upper_cm ?? null,
            'leader_lower_cm' => $hook->pivot->leader_lower_cm ?? null,
         ];
      })->toArray();
   }
}
