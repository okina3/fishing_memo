<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemoRodSeeder extends Seeder
{
   /**
    * Run the database seeds.
    *
    * @return void
    */
   public function run(): void
   {
      DB::table('memo_rods')->insert([
         //ユーザー１のダミーデータ
         [
            'memo_id' => 1,
            'rod_id' => 1,
            'main_line' => 2.0,
         ],
         [
            'memo_id' => 1,
            'rod_id' => 3,
            'main_line' => 2.5,
         ],
         [
            'memo_id' => 2,
            'rod_id' => 2,
            'main_line' => 1.5,
         ],
         [
            'memo_id' => 3,
            'rod_id' => 3,
            'main_line' => 2.5,
         ],

         //ユーザー２のダミーデータ
         [
            'memo_id' => 11,
            'rod_id' => 4,
            'main_line' => 3.0,
         ],
         [
            'memo_id' => 12,
            'rod_id' => 5,
            'main_line' => 1.5,
         ],
         [
            'memo_id' => 13,
            'rod_id' => 6,
            'main_line' => 2.5,
         ],

         //ユーザー３のダミーデータ
         [
            'memo_id' => 16,
            'rod_id' => 7,
            'main_line' => 20,
         ],
      ]);
   }
}
