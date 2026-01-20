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
         [
            'subject' => '緊急の問い合わせ',
            'user_id' => 1,
            'message' => ' 緊急の内容のテストメッセージ（ユーザー1）',
         ],
         [
            'subject' => '重要な問い合わせ',
            'user_id' => 2,
            'message' => '重要な内容のテストメッセージ（ユーザー2）',
         ],
         [
            'subject' => '迷惑メール報告',
            'user_id' => 3,
            'message' => '迷惑メールに関するテストメッセージ（ユーザー3）',
         ],
      ]);

      // ユーザー1のダミーデータを追加で20件作成（ペジネーション/テスト用）
      $additional = [];
      for ($i = 1; $i <= 20; $i++) {
         $additional[] = [
            'subject' => 'ダミー問い合わせ ' . $i,
            'user_id' => 1,
            'message' => 'ダミーメッセージ（ユーザー1） #' . $i,
            'created_at' => now(),
            'updated_at' => now(),
         ];
      }
      DB::table('contacts')->insert($additional);

      // ユーザー1のソフトデリートされたダミーデータを追加で20件作成（ペジネーション/テスト用）
      $softDeleted = [];
      for ($i = 1; $i <= 20; $i++) {
         $softDeleted[] = [
            'subject' => '削除済みダミー問い合わせ ' . $i,
            'user_id' => 1,
            'message' => '削除済みダミーメッセージ（ユーザー1） #' . $i,
            'created_at' => now(),
            'updated_at' => now(),
            'deleted_at' => now(),
         ];
      }
      DB::table('contacts')->insert($softDeleted);
   }
}
