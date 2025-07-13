<?php
namespace Database\Factories;

use App\Models\Game;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'game_id' => Game::factory(),
            'name' => $this->faker->words(3, true),
            'price' => $this->faker->numberBetween(10000, 500000),
            'is_active' => true,
        ];
    }
}