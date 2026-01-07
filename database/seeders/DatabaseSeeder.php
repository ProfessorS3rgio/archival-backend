<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            AdminUserSeeder::class,
            SangguniangPanlalawiganSeeder::class,
            SangguniangPanlalawiganMembersSeeder::class,
            ResolutionSeeder::class,
            OrdinanceSeeder::class,
            SettingsSeeder::class,
        ]);
    }
}
