<?php
namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class GameFactory extends Factory
{
    public function definition(): array
    {
        $name = $this->faker->words(2, true);
        return [
            'name' => ucwords($name),
            'slug' => Str::slug($name),
            'thumbnail' => 'game_thumbnails/placeholder.jpg',
            'category' => $this->faker->word,
            'is_active' => true,
        ];
    }
}