<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NivelesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('niveles')->insert([
            [
                'nombre' => 'Aprendizaje de letras y sílabas',
                'numero_lecciones' => 6,
            ],
            [
                'nombre' => 'Formación de Palabras y Lectura Básica',
                'numero_lecciones' => 4,
            ],
            [
                'nombre' => 'Aprendizaje',
                'numero_lecciones' => 3,
            ],
            [
                'nombre' => 'Aprendizaje',
                'numero_lecciones' => 5,
            ],
            [
                'nombre' => 'Aprendizaje',
                'numero_lecciones' => 3,
            ],
        ]);
    }
}
