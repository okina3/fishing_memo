<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class KeepBackFlashForAjax
{
   /**
    * Sessionを、次のリクエストまで保持するミドルウェア。
    */
   public function handle(Request $request, Closure $next)
   {
      // リクエストを処理してレスポンスを得る
      $response = $next($request);
      // Ajax / JSON リクエストでのみ実行する
      if ($request->ajax() || $request->wantsJson() || $request->header('X-Requested-With') === 'XMLHttpRequest') {
         // flash キーが存在する場合、該当キーのみ次リクエストまで保持する
         if (session()->has('back_button_clicked')) {
            session()->keep('back_button_clicked');
         }
      }

      return $response;
   }
}
