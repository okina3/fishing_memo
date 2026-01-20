<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
   ->withRouting(
      web: __DIR__ . '/../routes/web.php',
      commands: __DIR__ . '/../routes/console.php',
      health: '/up',
   )
   ->withMiddleware(function (Middleware $middleware): void {
      // 本番環境でのHTTPS終端に対応するための設定
      $middleware->trustProxies(at: '*', headers: Request::HEADER_X_FORWARDED_AWS_ELB);

      // 許可するホストを明示（サブドメインなし）
      $middleware->trustHosts(at: ['fishing-memo-app.link']);

      // マルチログイン用のSession Cookie のミドルウェア設定
      $middleware->prependToGroup('web', \App\Http\Middleware\AdminSessionCookie::class);

      // 未認証時のリダイレクト先を URL に応じて分岐するミドルウェアの設定
      $middleware->alias([
         'auth' => \App\Http\Middleware\Authenticate::class,
      ]);
   })
   ->withExceptions(function (Exceptions $exceptions): void {
      //
   })->create();
