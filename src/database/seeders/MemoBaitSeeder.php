<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemoBaitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('memo_baits')->insert([
            //ユーザー１のダミーデータ
            [
                'memo_id' => 1,
                'bait_id' => 1,
            ],
            [
                'memo_id' => 1,
                'bait_id' => 3,
            ],
            [
                'memo_id' => 2,
                'bait_id' => 2,
            ],
            [
                'memo_id' => 2,
                'bait_id' => 3,
            ],
            [
                'memo_id' => 3,
                'bait_id' => 3,
            ],
            [
                'memo_id' => 3,
                'bait_id' => 1,
            ],

            //ユーザー２のダミーデータ
            [
                'memo_id' => 11,
                'bait_id' => 4,
            ],
            [
                'memo_id' => 12,
                'bait_id' => 5,
            ],
            [
                'memo_id' => 13,
                'bait_id' => 6,
            ],

            //ユーザー３のダミーデータ
            [
                'memo_id' => 16,
                'bait_id' => 7,
            ],
            [
                'memo_id' => 17,
                'bait_id' => 8,
            ],
            [
                'memo_id' => 18,
                'bait_id' => 9,
            ],

            //ユーザー４のダミーデータ
            [
                'memo_id' => 21,
                'bait_id' => 10,
            ],
            [
                'memo_id' => 22,
                'bait_id' => 11,
            ],
            [
                'memo_id' => 23,
                'bait_id' => 12,
            ],

            //ユーザー５のダミーデータ
            [
                'memo_id' => 26,
                'bait_id' => 13,
            ],
            [
                'memo_id' => 27,
                'bait_id' => 14,
            ],
            [
                'memo_id' => 28,
                'bait_id' => 15,
            ],
        ]);
    }
}
