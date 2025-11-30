<?php

namespace Database\Factories;

use App\Models\FishName;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FishName>
 */
class FishNameFactory extends Factory
{
   /**
    * ファクトリが対応するモデル名。
    * @var string
    */
   protected $model = FishName::class;

   /**
    * モデルのデフォルト状態を定義。
    * @return array<string, mixed>
    */
   public function definition(): array
   {
      return [
         'user_id' => User::factory(),
         'name' => $this->faker->word(),
      ];
   }
}
