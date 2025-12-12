<?php

namespace Database\Factories;

use App\Models\Spot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Spot>
 */
class SpotFactory extends Factory
{
   /**
    * ファクトリが対応するモデル名。
    * @var string
    */
   protected $model = Spot::class;

   /**
    * モデルのデフォルト状態を定義。
    * @return array<string, mixed>
    */
   public function definition(): array
   {
      return [
         'user_id' => User::factory(),
         'name' => $this->faker->unique()->word(),
      ];
   }
}
