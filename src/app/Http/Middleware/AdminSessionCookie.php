<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminSessionCookie
{
   /**
    * 同一ブラウザでのユーザーと管理者の、Session cookie を切り替えるミドルウェア。
    */
   public function handle(Request $request, Closure $next)
   {
      if ($request->is('admin*')) {
         config(['session.cookie' => config('session.cookie_admin')]);
      }

      return $next($request);
   }
}
