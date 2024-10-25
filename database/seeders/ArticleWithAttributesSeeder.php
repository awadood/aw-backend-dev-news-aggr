<?php

namespace Database\Seeders;

use App\Models\Article;
use App\Models\Attribute;
use Illuminate\Database\Seeder;

class ArticleWithAttributesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Article::factory()
            ->count(10)
            ->has(Attribute::factory()->count(3))
            ->create();
    }
}
