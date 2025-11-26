<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ContactSeeder extends Seeder
{
   /**
    * @return void
    */
   public function run(): void
   {
      DB::table('contacts')->insert([
         [
            'subject' => 'テスト件名1',
            'user_id' => 1,
            'message' => 'テストメッセージ1（ユーザー1）',
         ],
         [
            'subject' => 'テスト件名2',
            'user_id' => 2,
            'message' => 'テストメッセージ1（ユーザー2）',
         ],
         [
            'subject' => 'テスト件名3',
            'user_id' => 3,
            'message' => 'テストメッセージ1（ユーザー3）',
         ],
      ]);
   }
}
