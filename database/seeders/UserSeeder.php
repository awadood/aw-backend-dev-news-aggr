<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::factory()->create([
            'name' => 'Abdul Wadood',
            'email' => 'candidate@innoscripta.com',
        ]);
        User::factory(3)->create();
    }
}
