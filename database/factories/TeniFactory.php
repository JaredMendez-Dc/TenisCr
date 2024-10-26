<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Teni>
 */
class TeniFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'color' => $this->faker->colorName,
            'talla' => $this->faker->randomElement(['S', 'M', 'L', 'XL']),
            'costo' => $this->faker->randomFloat(2, 20, 150),
            'marca_id' => $this->faker->numberBetween(1, 6),
            'categoria' => $this->faker->word,
            'imagen' => ''
        ];
    }
}
