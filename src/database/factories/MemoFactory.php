<?php

namespace Database\Factories;

use App\Models\Memo;
use App\Models\Spot;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Memo>
 */
class MemoFactory extends Factory
{
   /**
    * ファクトリが対応するモデル名。
    * @var string
    */
   protected $model = Memo::class;

   /**
    * モデルのデフォルト状態を定義。
    * @return array<string, mixed>
    */
   public function definition(): array
   {
      $start = $this->faker->time('H:i:s');
      // end_time は start_time のあと（最大6時間後）
      $end = date('H:i:s', strtotime($start) + rand(3600, 6 * 3600));

      return [
         'user_id' => User::factory(),
         'fishing_date' => $this->faker->date(),
         'start_time' => $start,
         'end_time' => $end,
         'weather' => $this->faker->randomElement(['晴れ', '曇り', '雨', 'その他']),
         'air_temp' => $this->faker->optional()->numberBetween(0, 35),
         'max_wind' => $this->faker->optional()->numberBetween(0, 30),
         'wind_dir' => $this->faker->optional()->randomElement(['北', '北東', '東', '南東', '南', '南西', '西', '北西']),
         'river_flow' => $this->faker->randomElement(['流れあり', '流れなし']),
         'turbidity' => $this->faker->optional()->randomElement(['クリア', 'やや濁り', '濁り', '強い濁り']),
         'debris' => $this->faker->randomElement(['なし', 'ややあり', 'あり']),
         'water_level' => $this->faker->optional()->randomFloat(1, 0, 99),
         'water_temp' => $this->faker->optional()->numberBetween(0, 35),
         'content' => $this->faker->paragraph(),
      ];
   }
}
