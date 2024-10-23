<?php

namespace Database\Factories;

use App\Models\Preference;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class PreferenceFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Preference::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'categories' => $this->faker->randomElement(['Technology, Health', 'Sports, Politics', 'Science, Entertainment']),
            'sources' => $this->faker->randomElement(['BBC, CNN', 'Al Jazeera, Fox News', 'Reuters, AP']),
            'authors' => $this->faker->name.', '.$this->faker->name,
        ];
    }
}
