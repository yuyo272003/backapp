<?php

namespace Progreso;

use App\Models\Leccion;
use App\Models\Progreso;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProgresoControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_actualizar_progreso_crea_registro_nuevo()
    {
        $user = User::factory()->create();
        $leccion = Leccion::factory()->create();

        $response = $this->postJson('/api/progreso', [
            'usuario_id' => $user->id,
            'leccion_id' => $leccion->id
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('progresos', [
            'usuario_id' => $user->id,
            'leccion_id' => $leccion->id
        ]);
    }

    public function test_actualizar_progreso_para_leccion_existente_no_duplica()
    {
        $user = User::factory()->create();
        $leccion = Leccion::factory()->create();

        // Crear progreso inicial
        Progreso::create([
            'usuario_id' => $user->id,
            'leccion_id' => $leccion->id,
            'nivel_id' => $leccion->nivel_id,
            'porcentaje' => 20
        ]);

        $response = $this->postJson('/api/progreso', [
            'usuario_id' => $user->id,
            'leccion_id' => $leccion->id
        ]);

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Esta lección ya fue registrada previamente.'
        ]);
    }
}

