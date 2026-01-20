<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class InterventionImageServiceProvider extends ServiceProvider
{
   /**
    * ImageManager インスタンスをサービスの登録
    * @return void
    */
   public function register(): void
   {
      $this->app->singleton(ImageManager::class, function () {
         return new ImageManager(new Driver());
      });
   }

   /**
    * ファサードエイリアスの登録
    * @return void
    */
   public function boot(): void
   {
      if (! class_exists('InterventionImage')) {
         if (class_exists('Intervention\\Image\\Laravel\\Facades\\Image', false)) {
            class_alias('Intervention\\Image\\Laravel\\Facades\\Image', 'InterventionImage');
         }
      }
   }
}
