<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SpotSeeder extends Seeder
{
   /**
    * Run the database seeds.
    */
   public function run(): void
   {
      DB::table('spots')->insert([
         //ユーザー１のダミーデータ
         [
            'user_id' => 1,
            'name' => 'ダミースポット1',
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'user_id' => 1,
            'name' => 'ダミースポット1-1',
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'user_id' => 1,
            'name' => 'ダミースポット1-2',
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         //ユーザー２のダミーデータ
         [
            'user_id' => 2,
            'name' => 'ダミースポット２',
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'user_id' => 2,
            'name' => 'ダミースポット２-1',
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'user_id' => 2,
            'name' => 'ダミースポット２-2',
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         //ユーザー３のダミーデータ
         [
            'user_id' => 3,
            'name' => 'ダミースポット３',
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'user_id' => 2,
            'name' => 'ダミースポット３-1',
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'user_id' => 2,
            'name' => 'ダミースポット３-2',
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         //ユーザー４のダミーデータ
         [
            'user_id' => 4,
            'name' => 'ダミースポット４',
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'user_id' => 4,
            'name' => 'ダミースポット４-1',
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'user_id' => 4,
            'name' => 'ダミースポット４-2',
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         //ユーザー５のダミーデータ
         [
            'user_id' => 5,
            'name' => 'ダミースポット５',
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'user_id' => 5,
            'name' => 'ダミースポット５-1',
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'user_id' => 5,
            'name' => 'ダミースポット５-2',
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
      ]);
   }
}
