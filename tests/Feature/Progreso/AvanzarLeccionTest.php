<?php

namespace Progreso;

use App\Models\Leccion;
use App\Models\User;
use App\Models\Progreso;
use App\Models\Nivel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AvanzarLeccionTest extends TestCase
{
    use RefreshDatabase;

    public function test_avanza_a_siguiente_leccion_y_actualiza_progreso()
    {
        $user = User::factory()->create();

        // Crear nivel primero
        $nivel = Nivel::factory()->create(['id' => 1]);

        // Ahora sí puedes crear lecciones asociadas a ese nivel
        $leccion1 = Leccion::factory()->create(['nivel_id' => $nivel->id, 'orden' => 1]);
        $leccion2 = Leccion::factory()->create(['nivel_id' => $nivel->id, 'orden' => 2]);

        // Crear progreso inicial en la lección 1
        Progreso::create([
            'usuario_id' => $user->id,
            'nivel_id' => $nivel->id,
            'leccion_id' => $leccion1->id,
            'porcentaje' => 0,
        ]);

        // Ejecutamos el avance
        $response = $this->actingAs($user)->post('/api/progreso/avanzar');

        $response->assertStatus(200);
        $response->assertJsonFragment([
            'message' => 'Progreso actualizado correctamente.',
        ]);

        // Verifica que avanzó a la lección 2
        $this->assertDatabaseHas('progreso', [
            'usuario_id' => $user->id,
            'nivel_id' => $nivel->id,
            'leccion_id' => $leccion2->id,
            'porcentaje' => 50,
        ]);
    }




    public function test_usuario_termina_todo_el_contenido()
    {
        $user = User::factory()->create();

        // Crear el nivel explícitamente
        $nivel = Nivel::factory()->create(['id' => 1]);

        // Crear solo una lección (la única disponible)
        Leccion::factory()->create(['nivel_id' => $nivel->id, 'orden' => 1]);

        // Simula que el usuario ya la terminó
        $this->actingAs($user)->post('/api/progreso/avanzar'); // primer avance
        $response = $this->actingAs($user)->post('/api/progreso/avanzar'); // ya no hay más lecciones

        $response->assertJsonFragment([
            'message' => 'Ya completaste todo el contenido.',
            'finalizado' => true,
        ]);
    }

    public function test_rechaza_avance_sin_usuario_o_leccion()
    {
        $response = $this->postJson('/api/progreso/avanzar');


        $response->assertStatus(401); // No autenticado
    }

    public function test_requiere_autenticacion_para_avanzar_progreso()
    {
        $response = $this->postJson('/api/progreso/avanzar');

        $response->assertStatus(401)
            ->assertJson(['message' => 'Unauthenticated.']);
    }


}
