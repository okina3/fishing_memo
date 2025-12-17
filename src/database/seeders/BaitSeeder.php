<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BaitSeeder extends Seeder
{
   /**
    * Run the database seeds.
    */
   public function run(): void
   {
      DB::table('baits')->insert([
         // ユーザー1のダミーデータ
         [
            'name' => '1-1 サンプルエサ',
            'user_id' => 1,
            'created_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => '1-2 サンプルエサ',
            'user_id' => 1,
            'created_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => '1-3 サンプルエサ',
            'user_id' => 1,
            'created_at' => '2023-10-01 11:11:11',
         ],

         // ユーザー2のダミーデータ
         [
            'name' => '2-1 サンプルエサ',
            'user_id' => 2,
            'created_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => '2-2 サンプルエサ',
            'user_id' => 2,
            'created_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => '2-3 サンプルエサ',
            'user_id' => 2,
            'created_at' => '2023-10-01 11:11:11',
         ],

         // ユーザー3のダミーデータ
         [
            'name' => '3-1 サンプルエサ',
            'user_id' => 3,
            'created_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => '3-2 サンプルエサ',
            'user_id' => 3,
            'created_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => '3-3 サンプルエサ',
            'user_id' => 3,
            'created_at' => '2023-10-01 11:11:11',
         ],

         // ユーザー4のダミーデータ
         [
            'name' => '4-1 サンプルエサ',
            'user_id' => 4,
            'created_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => '4-2 サンプルエサ',
            'user_id' => 4,
            'created_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => '4-3 サンプルエサ',
            'user_id' => 4,
            'created_at' => '2023-10-01 11:11:11',
         ],

         // ユーザー5のダミーデータ
         [
            'name' => '5-1 サンプルエサ',
            'user_id' => 5,
            'created_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => '5-2 サンプルエサ',
            'user_id' => 5,
            'created_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => '5-3 サンプルエサ',
            'user_id' => 5,
            'created_at' => '2023-10-01 11:11:11',
         ],
      ]);
   }
}
