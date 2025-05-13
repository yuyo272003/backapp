<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LeccionesSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('lecciones')->insert([
            ['nivel_id' => 1, 'titulo' => 'Introducción a las vocales', 'orden' => 1],
            ['nivel_id' => 1, 'titulo' => 'Consonantes nasales', 'orden' => 2],
            ['nivel_id' => 1, 'titulo' => 'Consonantes explosivas', 'orden' => 3],
            ['nivel_id' => 1, 'titulo' => 'Consonantes de aire', 'orden' => 4],
            ['nivel_id' => 1, 'titulo' => 'Consonantes linguales', 'orden' => 5],
            ['nivel_id' => 1, 'titulo' => 'Consonantes especiales', 'orden' => 6],
            ['nivel_id' => 2, 'titulo' => 'Revisión y práctica de consonantes', 'orden' => 1],
            ['nivel_id' => 2, 'titulo' => 'Consonantes y vocales', 'orden' => 2],
            ['nivel_id' => 2, 'titulo' => 'Práctica de palabras', 'orden' => 3],
           // ['nivel_id' => 2, 'titulo' => 'Introducción a la escritura', 'orden' => 4],
            ['nivel_id' => 3, 'titulo' => 'Combinación de consonantes', 'orden' => 1],
            ['nivel_id' => 3, 'titulo' => 'Consonantes combinadas y vocales', 'orden' => 2],
            ['nivel_id' => 3, 'titulo' => 'Formación de palabras', 'orden' => 2],
            ['nivel_id' => 4, 'titulo' => 'Monosílabas', 'orden' => 1],
            ['nivel_id' => 4, 'titulo' => 'Bisílabas', 'orden' => 2],
            ['nivel_id' => 4, 'titulo' => 'Trisílabas', 'orden' => 3],
            ['nivel_id' => 4, 'titulo' => 'Polisílabas', 'orden' => 4],
            ['nivel_id' => 4, 'titulo' => 'Lectura de oraciones', 'orden' => 5],
//            ['nivel_id' => 5, 'titulo' => 'B y V', 'orden' => 1],
//            ['nivel_id' => 5, 'titulo' => 'C, S y Z', 'orden' => 2],
//            ['nivel_id' => 5, 'titulo' => 'Ll y Y', 'orden' => 3],
//            ['nivel_id' => 5, 'titulo' => 'R y RR', 'orden' => 4],
            ['nivel_id' => 5, 'titulo' => 'Acentuación', 'orden' => 1],
            ['nivel_id' => 5, 'titulo' => 'Introducción a los signos de puntuación', 'orden' => 2],
            ['nivel_id' => 5, 'titulo' => 'Párrafos cortos', 'orden' => 3],
        ]);
    }
}
