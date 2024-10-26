<?php

namespace Database\Seeders;

use App\Models\Preference;
use App\Models\User;
use Illuminate\Database\Seeder;

class PreferenceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach (User::all() as $user) {
            Preference::factory()->count(3)->create([
                'user_id' => $user->id,
            ]);
        }
    }
}
