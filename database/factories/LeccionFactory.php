<?php

namespace Database\Factories;

use App\Models\Nivel;
use Illuminate\Database\Eloquent\Factories\Factory;

class LeccionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'titulo' => $this->faker->word,
            'orden' => $this->faker->numberBetween(1, 10),
            'nivel_id' => Nivel::factory(), // ← solución
        ];
    }
}

