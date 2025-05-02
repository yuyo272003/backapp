<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Nivel;
use App\Models\Leccion;
use App\Models\Progreso;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AvanceNivelTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculo_porcentaje_avance_correcto()
    {
        $user = User::factory()->create();
        $nivel = Nivel::factory()->create();

        Leccion::factory()->count(4)->create(['nivel_id' => $nivel->id]);

        // Simula que el usuario completó 2 lecciones
        Leccion::where('nivel_id', $nivel->id)->take(2)->get()->each(function ($leccion) use ($user) {
            Progreso::create([
                'usuario_id' => $user->id,
                'nivel_id' => $leccion->nivel_id,
                'leccion_id' => $leccion->id,
                'porcentaje' => 0
            ]);
        });

        $controller = new \App\Http\Controllers\ProgresoController();
        $porcentaje = $this->invokeMethod($controller, 'calcularProgresoNivel', [$user->id, $nivel->id]);

        $this->assertEquals(50, $porcentaje);
    }

    protected function invokeMethod($obj, $methodName, array $params = [])
    {
        $refMethod = new \ReflectionMethod(get_class($obj), $methodName);
        $refMethod->setAccessible(true);
        return $refMethod->invokeArgs($obj, $params);
    }
}
