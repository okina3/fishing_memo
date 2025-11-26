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
            'name' => 'エサ１',
            'user_id' => 1,
            'created_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => 'エサ１-2',
            'user_id' => 1,
            'created_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => 'エサ１-3',
            'user_id' => 1,
            'created_at' => '2023-10-01 11:11:11',
         ],

         // ユーザー2のダミーデータ
         [
            'name' => 'エサ２',
            'user_id' => 2,
            'created_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => 'エサ２-1',
            'user_id' => 2,
            'created_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => 'エサ２-2',
            'user_id' => 2,
            'created_at' => '2023-10-01 11:11:11',
         ],

         // ユーザー3のダミーデータ
         [
            'name' => 'エサ３',
            'user_id' => 3,
            'created_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => 'エサ３-1',
            'user_id' => 3,
            'created_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => 'エサ３-2',
            'user_id' => 3,
            'created_at' => '2023-10-01 11:11:11',
         ],

         // ユーザー4のダミーデータ
         [
            'name' => 'エサ４',
            'user_id' => 4,
            'created_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => 'エサ４-1',
            'user_id' => 4,
            'created_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => 'エサ４-2',
            'user_id' => 4,
            'created_at' => '2023-10-01 11:11:11',
         ],

         // ユーザー5のダミーデータ
         [
            'name' => 'エサ５',
            'user_id' => 5,
            'created_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => 'エサ５-1',
            'user_id' => 5,
            'created_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => 'エサ５-2',
            'user_id' => 5,
            'created_at' => '2023-10-01 11:11:11',
         ],
      ]);
   }
}
