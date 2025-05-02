<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\User;
use App\Models\Leccion;
use App\Models\Progreso;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ProgresoTest extends TestCase
{
    use RefreshDatabase;

    public function test_calculo_porcentaje_cuando_no_hay_lecciones()
    {
        $controller = new \App\Http\Controllers\ProgresoController();

        $porcentaje = $this->invokeMethod($controller, 'calcularProgresoNivel', [1, 1]);
        $this->assertEquals(0, $porcentaje);
    }

    // Método auxiliar para invocar privados
    protected function invokeMethod($obj, $methodName, array $params = [])
    {
        $refMethod = new \ReflectionMethod(get_class($obj), $methodName);
        $refMethod->setAccessible(true);
        return $refMethod->invokeArgs($obj, $params);
    }
}
