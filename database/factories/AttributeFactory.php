<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\Attribute;
use Illuminate\Database\Eloquent\Factories\Factory;

class AttributeFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Attribute::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'article_id' => Article::factory(),
            'name' => $this->faker->randomElement(config('articles.attributes')),
            'value' => $this->faker->word,
        ];
    }
}
