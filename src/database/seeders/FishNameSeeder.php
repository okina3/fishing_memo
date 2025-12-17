<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FishNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('fish_names')->insert([
            // ユーザー1のダミーデータ
            [
                'name' => '1-1 ヘラブナ',
                'user_id' => 1,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],
            [
                'name' => '1-2 マブナ',
                'user_id' => 1,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],
            [
                'name' => '1-3 コイ',
                'user_id' => 1,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],

            // ユーザー２のダミーデータ
            [
                'name' => '2-1 ヘラブナ',
                'user_id' => 2,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],
            [
                'name' => '2-2 マブナ',
                'user_id' => 2,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],
            [
                'name' => '2-3 コイ',
                'user_id' => 2,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],

            // ユーザー３のダミーデータ
            [
                'name' => '3-1 ヘラブナ',
                'user_id' => 3,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],
            [
                'name' => '3-2 マブナ',
                'user_id' => 3,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],
            [
                'name' => '3-3 コイ',
                'user_id' => 3,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],

            // ユーザー４のダミーデータ
            [
                'name' => '4-1 ヘラブナ',
                'user_id' => 4,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],
            [
                'name' => '4-2 マブナ',
                'user_id' => 4,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],
            [
                'name' => '4-3 コイ',
                'user_id' => 4,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],

            // ユーザー５のダミーデータ
            [
                'name' => '5-1 ヘラブナ',
                'user_id' => 5,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],
            [
                'name' => '5-2 マブナ',
                'user_id' => 5,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],
            [
                'name' => '5-3 コイ',
                'user_id' => 5,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],
        ]);
    }
}
