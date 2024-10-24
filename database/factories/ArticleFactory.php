<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'title' => $this->faker->sentence,
            'content' => $this->faker->paragraphs(3, true),
            'category' => $this->faker->randomElement(['Technology', 'Health', 'Science', 'Politics', 'Sports', 'Entertainment']),
            'source' => $this->faker->randomElement(['BBC', 'CNN', 'Reuters', 'Al Jazeera', 'Fox News', 'AP']),
            'author' => $this->faker->name,
            'published_at' => $this->faker->dateTimeBetween('-1 year', 'now'),
        ];
    }
}
