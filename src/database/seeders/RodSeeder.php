<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RodSeeder extends Seeder
{
   /**
    * Run the database seeds.
    *
    * @return void
    */
   public function run(): void
   {
      DB::table('rods')->insert([
         //ユーザー１のダミーデータ
         [
            'name' => '1-1 サンプル竿 12尺',
            'user_id' => 1,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => '1-2 サンプル竿 14尺',
            'user_id' => 1,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => '1-3 サンプル竿 10尺',
            'user_id' => 1,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         //ユーザー２のダミーデータ
         [
            'name' => '2-1 サンプル竿 14尺',
            'user_id' => 2,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => '2-2 サンプル竿 10尺',
            'user_id' => 2,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => '2-3 サンプル竿 8尺',
            'user_id' => 2,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         //ユーザー３のダミーデータ
         [
            'name' => '3-1 サンプル竿 12尺',
            'user_id' => 3,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => '3-2 サンプル竿 10尺',
            'user_id' => 2,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => '3-3 サンプル竿 8尺',
            'user_id' => 2,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         //ユーザー４のダミーデータ
         [
            'name' => '4-1 サンプル竿 10尺',
            'user_id' => 4,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => '4-2 サンプル竿 8尺',
            'user_id' => 4,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => '4-3 サンプル竿 6尺',
            'user_id' => 4,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         //ユーザー５のダミーデータ
         [
            'name' => '5-1 サンプル竿 14尺',
            'user_id' => 5,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => '5-2 サンプル竿 12尺',
            'user_id' => 5,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
         [
            'name' => '5-3 サンプル竿 10尺',
            'user_id' => 5,
            'created_at' => '2023-10-01 11:11:11',
            'updated_at' => '2023-10-01 11:11:11',
         ],
      ]);

         // ユーザー1のダミーデータを追加で20件作成（ペジネーション/テスト用）
         $extraRods = [];
         for ($i = 1; $i <= 20; $i++) {
            $num = str_pad((string)$i, 2, '0', STR_PAD_LEFT);
            $extraRods[] = [
               'name' => "1-EX-竿 {$num}",
               'user_id' => 1,
               'created_at' => '2023-01-01 11:11:11',
               'updated_at' => '2023-01-01 11:11:11',
            ];
         }

         DB::table('rods')->insert($extraRods);
   }
}
