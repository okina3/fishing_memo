<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemoFishNameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('memo_fish_names')->insert([
            //ユーザー１のダミーデータ
            [
                'memo_id' => 1,
                'fish_name_id' => 1,
                'count' => 1,
                'length' => 10,
            ],
            [
                'memo_id' => 1,
                'fish_name_id' => 2,
                'count' => 2,
                'length' => 60,
            ],
            [
                'memo_id' => 2,
                'fish_name_id' => 2,
                'count' => 2,
                'length' => 20,
            ],
            [
                'memo_id' => 2,
                'fish_name_id' => 3,
                'count' => 1,
                'length' => 40,
            ],
            [
                'memo_id' => 3,
                'fish_name_id' => 3,
                'count' => 3,
                'length' => 30,
            ],
            [
                'memo_id' => 3,
                'fish_name_id' => 1,
                'count' => 2,
                'length' => 45,
            ],

            //ユーザー２のダミーデータ
            [
                'memo_id' => 11,
                'fish_name_id' => 4,
                'count' => 1,
                'length' => 10,
            ],
            [
                'memo_id' => 12,
                'fish_name_id' => 5,
                'count' => 2,
                'length' => 20,
            ],
            [
                'memo_id' => 13,
                'fish_name_id' => 6,
                'count' => 3,
                'length' => 30,
            ],

            //ユーザー３のダミーデータ
            [
                'memo_id' => 16,
                'fish_name_id' => 7,
                'count' => 1,
                'length' => 10,
            ],
            [
                'memo_id' => 17,
                'fish_name_id' => 8,
                'count' => 2,
                'length' => 20,
            ],
            [
                'memo_id' => 18,
                'fish_name_id' => 9,
                'count' => 3,
                'length' => 30,
            ],

            //ユーザー４のダミーデータ
            [
                'memo_id' => 21,
                'fish_name_id' => 10,
                'count' => 1,
                'length' => 10,
            ],
            [
                'memo_id' => 22,
                'fish_name_id' => 11,
                'count' => 2,
                'length' => 20,
            ],
            [
                'memo_id' => 23,
                'fish_name_id' => 12,
                'count' => 3,
                'length' => 30,
            ],

            //ユーザー５のダミーデータ
            [
                'memo_id' => 26,
                'fish_name_id' => 13,
                'count' => 1,
                'length' => 10,
            ],
            [
                'memo_id' => 27,
                'fish_name_id' => 14,
                'count' => 2,
                'length' => 20,
            ],
            [
                'memo_id' => 28,
                'fish_name_id' => 15,
                'count' => 3,
                'length' => 30,
            ],
        ]);
    }
}
