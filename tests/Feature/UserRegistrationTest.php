<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Leccion;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserRegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registro_usuario_crea_usuario_y_progreso()
    {
        $leccion = Leccion::factory()->create();

        $response = $this->postJson('/register', [
            'name' => 'Andres',
        ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('users', ['name' => 'Andres']);
        $this->assertDatabaseHas('progreso', ['nivel_id' => $leccion->nivel_id]);
    }
}
