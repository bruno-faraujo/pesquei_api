<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Peixe>
 */
class PeixeFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'nome' => $this->faker->name(),
            'nome_cientifico' => $this->faker->month() . ' ' . $this->faker->colorName(),
            'habitat' =>  'Água doce',
        ];
    }
}
