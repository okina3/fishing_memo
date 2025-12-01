<?php

namespace Database\Factories;

use App\Models\Bait;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Bait>
 */
class BaitFactory extends Factory
{
   /**
    * ファクトリが対応するモデル名。
    * @var string
    */
   protected $model = Bait::class;

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
