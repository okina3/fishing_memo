<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemoHookSeeder extends Seeder
{
   /**
    * Run the database seeds.
    *
    * @return void
    */
   public function run(): void
   {
      DB::table('memo_hooks')->insert([
         // ユーザー1のダミーデータ
         [
            'memo_id' => 1,
            'hook_id' => 1,
            'leader_size' => 2.0,
            'leader_upper_cm' => 30,
            'leader_lower_cm' => 40,
         ],
         [
            'memo_id' => 2,
            'hook_id' => 3,
            'leader_size' => 2.5,
            'leader_upper_cm' => 25,
            'leader_lower_cm' => 0,
         ],
         [
            'memo_id' => 2,
            'hook_id' => 2,
            'leader_size' => 1.5,
            'leader_upper_cm' => 0,
            'leader_lower_cm' => 30,
         ],

         // ユーザー2のダミーデータ
         [
            'memo_id' => 11,
            'hook_id' => 4,
            'leader_size' => 3.0,
            'leader_upper_cm' => 35,
            'leader_lower_cm' => 0,
         ],
         [
            'memo_id' => 11,
            'hook_id' => 5,
            'leader_size' => 1.5,
            'leader_upper_cm' => 0,
            'leader_lower_cm' => 45,
         ],
         [
            'memo_id' => 13,
            'hook_id' => 6,
            'leader_size' => 2.5,
            'leader_upper_cm' => 28,
            'leader_lower_cm' => 38,
         ],

         // ユーザー3のダミーデータ
         [
            'memo_id' => 16,
            'hook_id' => 7,
            'leader_size' => 2.0,
            'leader_upper_cm' => 40,
            'leader_lower_cm' => 50,
         ],
      ]);
   }
}
