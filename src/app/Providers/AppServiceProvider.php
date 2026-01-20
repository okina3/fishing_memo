<?php

namespace App\Providers;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
   /**
    * Register any application services.
    */
   public function register(): void
   {
      //
   }

   /**
    * Bootstrap any application services.
    */
   public function boot(): void
   {
      // 本番環境では常にHTTPSスキームでURLを生成する
      if (App::environment('production')) {
         URL::forceScheme('https');
      }
   }
}
