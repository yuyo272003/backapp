<?php

namespace Progreso;

use App\Models\Progreso;
use App\Models\User;
use App\Models\Nivel;
use App\Models\Leccion;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VerProgresoTest extends TestCase
{
    use RefreshDatabase;

    public function test_usuario_ve_progreso_con_niveles_completados()
    {
        $user = User::factory()->create();

        $nivel = Nivel::create([
            'nombre' => 'Nivel 1',
            'numero_lecciones' => 5,
        ]);

        $leccion = Leccion::create([
            'nivel_id' => $nivel->id,
            'titulo' => 'Lección 1',
            'orden' => 1,
        ]);

        // Ahora sí puedes crear el progreso
        Progreso::create([
            'usuario_id' => $user->id,
            'nivel_id' => $nivel->id,
            'leccion_id' => $leccion->id,
            'porcentaje' => 100,
            'niveles_completados' => 2,
        ]);

        $response = $this->actingAs($user)->get('/api/progreso');

        $response->assertStatus(200)
            ->assertJsonFragment(['niveles_completados' => 2]);
    }


//    public function test_usuario_sin_progreso_devuelve_0()
//    {
//        $user = User::factory()->create();
//
//        $response = $this->actingAs($user)->get('/api/progreso');
//
//        $response->assertStatus(200)
//            ->assertJsonFragment(['niveles_completados' => 0]);
//    }
//
//    public function test_usuario_no_autenticado_no_puede_ver_progreso()
//    {
//        $response = $this->get('/api/progreso');
//        $response->assertStatus(401); // Unauthorized
//    }




}
