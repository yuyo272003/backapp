<?php

namespace Progreso;

use Tests\TestCase;

class CalcularPorcentajeTest extends TestCase
{
    public function test_calculo_porcentaje_cuando_no_hay_lecciones()
    {
        $controller = new \App\Http\Controllers\ProgresoController();
        $porcentaje = $this->invokeMethod($controller, 'calcularProgresoNivel', [1, 1]);
        $this->assertEquals(0, $porcentaje);
    }

    public function test_calculo_porcentaje_exacto_según_lecciones()
    {
        $user = User::factory()->create();
        $nivel = Nivel::factory()->create();
        Leccion::factory()->count(5)->create(['nivel_id' => $nivel->id]);

        $completadas = Leccion::where('nivel_id', $nivel->id)->take(3)->get();
        foreach ($completadas as $leccion) {
            Progreso::create([
                'usuario_id' => $user->id,
                'nivel_id' => $leccion->nivel_id,
                'leccion_id' => $leccion->id,
                'porcentaje' => 0
            ]);
        }

        $controller = new \App\Http\Controllers\ProgresoController();
        $resultado = $this->invokeMethod($controller, 'calcularProgresoNivel', [$user->id, $nivel->id]);
        $this->assertEquals(60, $resultado);
    }



}
