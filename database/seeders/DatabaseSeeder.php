<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(ArticleWithAttributesSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(PreferenceSeeder::class);
    }
}
