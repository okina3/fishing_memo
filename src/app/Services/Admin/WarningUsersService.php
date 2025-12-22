<?php

namespace App\Services\Admin;

use App\Models\Image;
use App\Models\User;
use App\Services\User\ImageService;
use Illuminate\Support\Facades\DB;

class WarningUsersService
{
   /**
    * 選択したユーザー、関連データを完全削除するメソッド。
    * @param int $userId
    * @return void
    */
   public static function permanentlyDeleteUser(int $userId): void
   {
      DB::transaction(function () use ($userId) {
         // 対象ユーザーを取得
         $user = User::onlyTrashed()->availableSelectUser($userId)->firstOrFail();

         // Storage 内の画像ファイルも削除
         $filenames = Image::where('user_id', $user->id)->pluck('filename');
         $filenames->each(fn($filename) => ImageService::deleteStorage($filename));

         // ユーザーを完全削除
         $user->forceDelete();
      }, 10);
   }
}
