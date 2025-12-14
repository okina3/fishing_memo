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
            'name' => 'ダミースポット１',
            'user_id' => 1,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => 'ダミースポット1-1',
            'user_id' => 1,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => 'ダミースポット1-2',
            'user_id' => 1,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         //ユーザー２のダミーデータ
         [
            'name' => 'ダミースポット２',
            'user_id' => 2,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => 'ダミースポット２-1',
            'user_id' => 2,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => 'ダミースポット２-2',
            'user_id' => 2,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         //ユーザー３のダミーデータ
         [
            'name' => 'ダミースポット３',
            'user_id' => 3,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => 'ダミースポット３-1',
            'user_id' => 2,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => 'ダミースポット３-2',
            'user_id' => 2,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         //ユーザー４のダミーデータ
         [
            'name' => 'ダミースポット４',
            'user_id' => 4,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => 'ダミースポット４-1',
            'user_id' => 4,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => 'ダミースポット４-2',
            'user_id' => 4,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         //ユーザー５のダミーデータ
         [
            'name' => 'ダミースポット５',
            'user_id' => 5,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => 'ダミースポット５-1',
            'user_id' => 5,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => 'ダミースポット５-2',
            'user_id' => 5,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
      ]);
   }
}
