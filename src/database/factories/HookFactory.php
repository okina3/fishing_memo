<?php

namespace Database\Factories;

use App\Models\Hook;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Hook>
 */
class HookFactory extends Factory
{
   /**
    * ファクトリが対応するモデル名。
    * @var string
    */
   protected $model = Hook::class;

   /**
    * モデルのデフォルト状態を定義。
    * @return array<string, mixed>
    */
   public function definition()
   {
      return [
         'user_id' => User::factory(),
         'name' => $this->faker->unique()->word(),
      ];
   }
}
