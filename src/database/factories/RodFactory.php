<?php

namespace Database\Factories;

use App\Models\Rod;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Rod>
 */
class RodFactory extends Factory
{
   /**
    * ファクトリが対応するモデル名。
    * @var string
    */
   protected $model = Rod::class;

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
