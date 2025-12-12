<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MemoSpotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('memo_spots')->insert([
            //ユーザー１のダミーデータ
            [
                'memo_id' => 1,
                'spot_id' => 1,
            ],
            [
                'memo_id' => 2,
                'spot_id' => 2,
            ],
            [
                'memo_id' => 3,
                'spot_id' => 3,
            ],

            //ユーザー２のダミーデータ
            [
                'memo_id' => 11,
                'spot_id' => 4,
            ],
            [
                'memo_id' => 12,
                'spot_id' => 5,
            ],
            [
                'memo_id' => 13,
                'spot_id' => 6,
            ],

            //ユーザー３のダミーデータ
            [
                'memo_id' => 16,
                'spot_id' => 7,
            ],
        ]);
    }
}
