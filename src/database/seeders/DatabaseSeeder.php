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
            SpotSeeder::class,
            MemoSeeder::class,
            ImageSeeder::class,
            BaitSeeder::class,
            FishNameSeeder::class,
            MemoImageSeeder::class,
            MemoFishNameSeeder::class,
            MemoBaitSeeder::class,
            MemoSpotSeeder::class,
            ShareSettingsSeeder::class,
            ContactSeeder::class,
        ]);
    }
}
