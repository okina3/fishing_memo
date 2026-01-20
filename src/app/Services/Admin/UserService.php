<?php

namespace App\Services\Admin;

use App\Models\ShareSetting;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class UserService
{
   /**
    * 全ユーザー、また、検索したユーザーを表示するメソッド。
    * @param string|null $keyword
    * @param int $perPage
    * @return LengthAwarePaginator
    */
   public static function allUsers(?string $keyword, int $perPage = 15)
   {
      return User::availableAllUsers()
         ->searchKeyword($keyword)
         ->paginate($perPage)
         ->withQueryString();
   }

   /**
    * 停止ユーザーの、共有されているメモの、共有設定を解除するメソッド。
    * @param int $request_user_id
    * @return void
    */
   public static function deleteUserShareSettingAll(int $request_user_id): void
   {
      // 停止ユーザーが、共有しているメモの共有を解除
      $user_memos = User::with('memos.shareSettings')->availableSelectUser($request_user_id)->first();
      foreach ($user_memos->memos as $user_memo) {
         foreach ($user_memo->shareSettings as $shareSetting) {
            $shareSetting->delete();
         }
      }
      // 停止ユーザーに、共有しているメモの共有を解除
      $share_settings = ShareSetting::where('sharing_user_id', $request_user_id)->get();
      foreach ($share_settings as $share_setting) {
         $share_setting->delete();
      }
   }
}
