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
                'name' => '魚１',
                'user_id' => 1,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],
            [
                'name' => '魚１-2',
                'user_id' => 1,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],
            [
                'name' => '魚１-3',
                'user_id' => 1,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],

            // ユーザー２のダミーデータ
            [
                'name' => '魚２',
                'user_id' => 2,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],
            [
                'name' => '魚２-1',
                'user_id' => 2,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],
            [
                'name' => '魚２-2',
                'user_id' => 2,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],

            // ユーザー３のダミーデータ
            [
                'name' => '魚３',
                'user_id' => 3,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],
            [
                'name' => '魚３-1',
                'user_id' => 3,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],
            [
                'name' => '魚３-2',
                'user_id' => 3,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],

            // ユーザー４のダミーデータ
            [
                'name' => '魚４',
                'user_id' => 4,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],
            [
                'name' => '魚４-1',
                'user_id' => 4,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],
            [
                'name' => '魚４-2',
                'user_id' => 4,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],

            // ユーザー５のダミーデータ
            [
                'name' => '魚５',
                'user_id' => 5,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],
            [
                'name' => '魚５-1',
                'user_id' => 5,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],
            [
                'name' => '魚５-2',
                'user_id' => 5,
                'created_at' => '2023-10-01 11:11:11',
                'updated_at' => '2023-10-01 11:11:11',
            ],
        ]);
    }
}
