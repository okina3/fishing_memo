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
                'river_flow' => '流れあり',
                'turbidity' => '濁り',
                'water_level' => 2.3,
                'water_temp' => 10,
            ],
            [
                'memo_id' => 2,
                'spot_id' => 2,
                'river_flow' => '流れなし',
                'turbidity' => 'クリア',
                'water_level' => 1.9,
                'water_temp' => 15,
            ],
            [
                'memo_id' => 3,
                'spot_id' => 3,
                'river_flow' => '流れあり',
                'turbidity' => 'クリア',
                'water_level' => 2.8,
                'water_temp' => 20,
            ],

            //ユーザー２のダミーデータ
            [
                'memo_id' => 11,
                'spot_id' => 4,
                'river_flow' => '流れあり',
                'turbidity' => '濁り',
                'water_level' => 3.0,
                'water_temp' => 25,
            ],
            [
                'memo_id' => 12,
                'spot_id' => 5,
                'river_flow' => '流れなし',
                'turbidity' => '濁り',
                'water_level' => 1.9,
                'water_temp' => 15,
            ],
            [
                'memo_id' => 13,
                'spot_id' => 6,
                'river_flow' => '流れあり',
                'turbidity' => 'クリア',
                'water_level' => 2.5,
                'water_temp' => 18,
            ],

            //ユーザー３のダミーデータ
            [
                'memo_id' => 16,
                'spot_id' => 7,
                'river_flow' => '流れなし',
                'turbidity' => '濁り',
                'water_level' => 2.2,
                'water_temp' => 12,
            ],
        ]);
    }
}
