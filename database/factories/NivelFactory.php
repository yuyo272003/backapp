<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class NivelFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nombre' => $this->faker->word,
            'numero_lecciones' => $this->faker->numberBetween(1, 10), // ← agrega esto
        ];
    }
}

