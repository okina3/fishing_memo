<?php

namespace Database\Factories;

use App\Models\Memo;
use App\Models\ShareSetting;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShareSetting>
 */
class ShareSettingFactory extends Factory
{
   /**
    * ファクトリが対応するモデル名。
    * @var string
    */
   protected $model = ShareSetting::class;

   /**
    * モデルのデフォルト状態を定義。
    * @return array<string, mixed>
    */
   public function definition(): array
   {
      return [
         'sharing_user_id' => User::factory(),
         'memo_id' => Memo::factory(),
         // 編集権限はランダム（30% 真）
         'edit_access' => $this->faker->boolean(30),
      ];
   }
}
