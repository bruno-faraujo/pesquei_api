<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Pescado>
 */
class PescadoFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'ponto_id' => 1,
            'peixe_id' => rand(1,15),
            'comprimento' => rand(18,125),
            'peso' => rand(100,2150)
        ];
    }
}
