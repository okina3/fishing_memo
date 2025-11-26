<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;

class Authenticate extends Middleware
{
   /**
    * 未認証時のリダイレクト先を返すミドルウェア。
    */
   protected function redirectTo($request): ?string
   {
      if ($request->expectsJson()) {
         return null;
      }
      // 管理者用のログインページへリダイレクト
      if ($request->is('admin*')) {
         return route('admin.login');
      }

      // ユーザー用のログインページへリダイレクト
      return route('login');
   }
}
