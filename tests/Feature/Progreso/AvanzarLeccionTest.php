<?php

namespace Progreso;

use App\Models\Leccion;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvanzarLeccionTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_avanza_a_siguiente_leccion()
    {
        $user = User::factory()->create();

        Leccion::factory()->create(['nivel_id' => 1, 'orden' => 1]);
        Leccion::factory()->create(['nivel_id' => 1, 'orden' => 2]);

        $response = $this->actingAs($user)->post('/api/progreso/avanzar');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Progreso actualizado correctamente',
        ]);
    }

    public function test_usuario_termina_todo_el_contenido()
    {
        $user = User::factory()->create();
        Leccion::factory()->create(['nivel_id' => 1, 'orden' => 1]);

        // Simula que ya completó la única lección
        $response = $this->actingAs($user)->post('/api/progreso/avanzar');
        $response = $this->actingAs($user)->post('/api/progreso/avanzar');

        $response->assertJsonFragment([
            'message' => 'Ya completaste todo el contenido.',
            'finalizado' => true,
        ]);
    }
}
