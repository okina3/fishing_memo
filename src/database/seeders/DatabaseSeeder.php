<?php

namespace Database\Seeders;

// use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    // use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $this->call([
            AdminSeeder::class,
            UserSeeder::class,
            MemoSeeder::class,
            SpotSeeder::class,
            RodSeeder::class,
            HookSeeder::class,
            BaitSeeder::class,
            FishNameSeeder::class,
            ImageSeeder::class,
            MemoSpotSeeder::class,
            MemoRodSeeder::class,
            MemoHookSeeder::class,
            MemoBaitSeeder::class,
            MemoFishNameSeeder::class,
            MemoImageSeeder::class,
            ShareSettingsSeeder::class,
            ContactSeeder::class,
        ]);
    }
}
