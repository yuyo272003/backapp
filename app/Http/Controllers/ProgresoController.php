<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Progreso;
use App\Models\Leccion;
use App\Models\Nivel;

class ProgresoController extends Controller
{
    /**
     * Registrar una lección completada y actualizar el porcentaje de progreso en el nivel.
     */
    public function actualizarProgreso(Request $request)
    {
        Log::info("🎯 Se recibió petición de progreso:", $request->all());

        $request->validate([
            'usuario_id' => 'required|exists:users,id', // asegúrate que la tabla es users
            'leccion_id' => 'required|exists:lecciones,id',
        ]);

        $usuarioId = $request->usuario_id;
        $leccionId = $request->leccion_id;

        // Obtener la lección y su nivel
        $leccion = Leccion::findOrFail($leccionId);
        $nivelId = $leccion->nivel_id;

        // Verificar si ya existe un progreso registrado para esta lección
        $progresoExistente = Progreso::where('usuario_id', $usuarioId)
            ->where('leccion_id', $leccionId)
            ->first();

        if ($progresoExistente) {
            return response()->json([
                'message' => 'Esta lección ya fue registrada previamente.',
                'progreso' => $progresoExistente
            ], 200);
        }

        // Crear el nuevo progreso
        Progreso::create([
            'usuario_id' => $usuarioId,
            'nivel_id' => $nivelId,
            'leccion_id' => $leccionId,
            'porcentaje' => 0,
        ]);

        // Recalcular el porcentaje total del nivel
        $porcentaje = $this->calcularProgresoNivel($usuarioId, $nivelId);

        // Actualizar el porcentaje en todos los progresos de este nivel para el usuario
        Progreso::where('usuario_id', $usuarioId)
            ->where('nivel_id', $nivelId)
            ->update(['porcentaje' => $porcentaje]);

        // Si el nivel ya se completó al 100%, actualizamos los niveles completados del usuario
        if ($porcentaje == 100) {
            $this->actualizarConteoNivelesCompletados($usuarioId);
        }

        return response()->json([
            'message' => 'Progreso actualizado correctamente',
            'porcentaje' => round($porcentaje, 2),
            'nivel_id' => $nivelId
        ]);
    }

    /**
     * Calcula el porcentaje de progreso en un nivel según lecciones completadas.
     */
    private function calcularProgresoNivel($usuarioId, $nivelId)
    {
        $totalLecciones = Leccion::where('nivel_id', $nivelId)->count();

        $leccionesCompletadas = Progreso::where('usuario_id', $usuarioId)
            ->where('nivel_id', $nivelId)
            ->distinct('leccion_id')
            ->count('leccion_id');

        return ($totalLecciones > 0) ? ($leccionesCompletadas / $totalLecciones) * 100 : 0;
    }

    /**
     * Cuenta cuántos niveles completos tiene el usuario y actualiza ese dato en todos sus progresos.
     */
    private function actualizarConteoNivelesCompletados($usuarioId)
    {
        $nivelesCompletados = Progreso::where('usuario_id', $usuarioId)
            ->where('porcentaje', 100)
            ->distinct('nivel_id')
            ->count('nivel_id');

        Progreso::where('usuario_id', $usuarioId)
            ->update(['niveles_completados' => $nivelesCompletados]);
    }

    /**
     * Devuelve el resumen del progreso por nivel del usuario.
     */
    public function obtenerProgresoPorNivel($usuarioId)
    {
        $niveles = Nivel::with('lecciones')->get();
        $resumen = [];

        foreach ($niveles as $nivel) {
            $porcentaje = $this->calcularProgresoNivel($usuarioId, $nivel->id);

            $resumen[] = [
                'nivel_id' => $nivel->id,
                'nombre' => $nivel->nombre,
                'porcentaje' => round($porcentaje, 2),
                'total_lecciones' => $nivel->lecciones->count(),
            ];
        }

        return response()->json($resumen);
    }

    public function verProgreso(Request $request)
    {
        $user = auth()->user(); // ✅ No uses $request->user() directamente si está fallando
        $progreso = $user->progreso;

        return response()->json([
            'niveles_completados' => $progreso->niveles_completados ?? 0
        ]);
    }

    // Añadir este endpoint en tu controlador
    public function obtenerLeccionId(Request $request)
    {
        $userId = auth()->id();

        $progreso = \App\Models\Progreso::where('usuario_id', $userId)
            ->orderByDesc('id') // o usa 'created_at' si prefieres
            ->first();

        return response()->json([
            'leccion_id' => $progreso ? $progreso->leccion_id : 1,
        ]);
    }




    public function avanzar(Request $request)
    {
        $user = $request->user();

        // 1) Buscar o crear progreso del usuario
        $progreso = Progreso::firstOrNew([
            'usuario_id' => $user->id,
        ]);

        $nivelActual   = $progreso->nivel_id   ?? 1;
        $leccionActual = $progreso->leccion_id ?? 1;

        $ordenActual = Leccion::where('nivel_id', $nivelActual)
            ->where('id', $leccionActual)
            ->value('orden') ?? 1;

        $nivelAnterior = $nivelActual;

        // 2) Buscar siguiente lección en el mismo nivel
        $siguiente = Leccion::where('nivel_id', $nivelActual)
            ->where('orden', '>', $ordenActual)
            ->orderBy('orden')
            ->first();

        // 3) Si no hay más lecciones en este nivel, pasar al siguiente nivel
        if (! $siguiente) {
            $nivelActual++;
            $siguiente = Leccion::where('nivel_id', $nivelActual)
                ->orderBy('orden')
                ->first();
        }

        // 4) Si no hay siguiente nivel tampoco, ya terminó todo el contenido
        if (! $siguiente) {
            return response()->json([
                'message' => 'Ya completaste todo el contenido.',
                'finalizado' => true,
            ], 200);
        }

        // 5) Calcular porcentaje de avance en el nuevo nivel
        $totalLecciones = Leccion::where('nivel_id', $siguiente->nivel_id)->count();
        $ordenSig       = $siguiente->orden;
        $porcentaje     = round((($ordenSig - 1) / $totalLecciones) * 100, 2);

        // 6) Si cambió de nivel, aumentar niveles completados
        if ($siguiente->nivel_id > $nivelAnterior) {
            $progreso->niveles_completados = ($progreso->niveles_completados ?? 0) + 1;
        }

        // 7) Guardar progreso actualizado
        $progreso->nivel_id   = $siguiente->nivel_id;
        $progreso->leccion_id = $siguiente->id;
        $progreso->porcentaje = $porcentaje;
        $progreso->save();

        // 8) Respuesta
        return response()->json([
            'message'             => 'Progreso actualizado correctamente.',
            'nivel_id'            => $siguiente->nivel_id,
            'leccion_id'          => $siguiente->id,
            'porcentaje'          => $porcentaje,
            'niveles_completados' => $progreso->niveles_completados ?? 0,
            'finalizado'          => false,
        ], 200);
    }

    public function avanzarVowelMatchGame(Request $request)
    {
        $user = $request->user(); // 👈 Funciona con Sanctum

        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        // 🔐 Esta pantalla es del nivel 1, lección 1
        $nivelPantalla = 1;
        $leccionPantalla = 1;

        // Obtener o crear progreso del usuario
        $progreso = Progreso::firstOrNew(['usuario_id' => $user->id]);

        // Si ya avanzó más, no actualizar nada
        if (
            $progreso->nivel_id > $nivelPantalla ||
            ($progreso->nivel_id == $nivelPantalla && $progreso->leccion_id > $leccionPantalla)
        ) {
            return response()->json([
                'message' => 'Esta pantalla ya fue superada. No se modificó el progreso.',
                'repeticion' => true,
            ]);
        }

        // Buscar la lección siguiente dentro del mismo nivel
        $ordenActual = Leccion::where('nivel_id', $nivelPantalla)
            ->where('id', $leccionPantalla)
            ->value('orden') ?? 1;

        $siguiente = Leccion::where('nivel_id', $nivelPantalla)
            ->where('orden', '>', $ordenActual)
            ->orderBy('orden')
            ->first();

        if (!$siguiente) {
            // Si no hay más lecciones en el nivel actual, avanzar al siguiente nivel
            $siguiente = Leccion::where('nivel_id', $nivelPantalla + 1)
                ->orderBy('orden')
                ->first();

            if ($siguiente) {
                $progreso->niveles_completados = ($progreso->niveles_completados ?? 0) + 1;
            }
        }

        if (!$siguiente) {
            return response()->json([
                'message' => 'Ya completaste todas las lecciones disponibles.',
                'finalizado' => true,
            ]);
        }

        // Calcular nuevo porcentaje
        $totalLecciones = Leccion::where('nivel_id', $siguiente->nivel_id)->count();
        $ordenSiguiente = $siguiente->orden;
        $porcentaje = round((($ordenSiguiente - 1) / $totalLecciones) * 100, 2);

        // Actualizar progreso
        $progreso->nivel_id = $siguiente->nivel_id;
        $progreso->leccion_id = $siguiente->id;
        $progreso->porcentaje = $porcentaje;
        $progreso->save();

        return response()->json([
            'message' => 'Progreso actualizado correctamente',
            'nivel_id' => $siguiente->nivel_id,
            'leccion_id' => $siguiente->id,
            'porcentaje' => $porcentaje,
            'niveles_completados' => $progreso->niveles_completados ?? 0,
        ]);
    }

    public function avanzarLeccion(Request $request, int $nivelPantalla, int $leccionPantalla)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['error' => 'Usuario no autenticado'], 401);
        }

        $progreso = Progreso::firstOrNew(['usuario_id' => $user->id]);

        if (
            $progreso->nivel_id > $nivelPantalla ||
            ($progreso->nivel_id == $nivelPantalla && $progreso->leccion_id > $leccionPantalla)
        ) {
            return response()->json([
                'message' => 'Esta pantalla ya fue superada. No se modificó el progreso.',
                'repeticion' => true,
            ]);
        }

        $ordenActual = Leccion::where('nivel_id', $nivelPantalla)
            ->where('id', $leccionPantalla)
            ->value('orden') ?? 1;

        $siguiente = Leccion::where('nivel_id', $nivelPantalla)
            ->where('orden', '>', $ordenActual)
            ->orderBy('orden')
            ->first();

        if (!$siguiente) {
            $siguiente = Leccion::where('nivel_id', $nivelPantalla + 1)
                ->orderBy('orden')
                ->first();

            if ($siguiente) {
                $progreso->niveles_completados = ($progreso->niveles_completados ?? 0) + 1;
            }
        }

        if (!$siguiente) {
            return response()->json([
                'message' => 'Ya completaste todas las lecciones disponibles.',
                'finalizado' => true,
            ]);
        }

        $totalLecciones = Leccion::where('nivel_id', $siguiente->nivel_id)->count();
        $ordenSiguiente = $siguiente->orden;
        $porcentaje = round((($ordenSiguiente - 1) / $totalLecciones) * 100, 2);

        $progreso->nivel_id = $siguiente->nivel_id;
        $progreso->leccion_id = $siguiente->id;
        $progreso->porcentaje = $porcentaje;
        $progreso->save();

        return response()->json([
            'message' => 'Progreso actualizado correctamente',
            'nivel_id' => $siguiente->nivel_id,
            'leccion_id' => $siguiente->id,
            'porcentaje' => $porcentaje,
            'niveles_completados' => $progreso->niveles_completados ?? 0,
        ]);
    }
    public function avanzarLeccion1(Request $request)
    {
        return $this->avanzarLeccion($request, 1, 1);
    }

    public function avanzarLeccion2(Request $request)
    {
        return $this->avanzarLeccion($request, 1, 2);
    }

    public function avanzarLeccion3(Request $request)
    {
        return $this->avanzarLeccion($request, 1, 3);
    }

    public function avanzarLeccion4(Request $request)
    {
        return $this->avanzarLeccion($request, 1, 4);
    }

    public function avanzarLeccion5(Request $request)
    {
        return $this->avanzarLeccion($request, 1, 5);
    }

    public function avanzarLeccion6(Request $request)
    {
        return $this->avanzarLeccion($request, 1, 6);
    }

    public function avanzarLeccion7(Request $request)
    {
        return $this->avanzarLeccion($request, 2, 1);
    }

    public function avanzarLeccion8(Request $request)
    {
        return $this->avanzarLeccion($request, 2, 2);
    }

    public function avanzarLeccion9(Request $request)
    {
        return $this->avanzarLeccion($request, 2, 3);
    }

    public function avanzarLeccion10(Request $request)
    {
        return $this->avanzarLeccion($request, 2, 4);
    }

    public function avanzarLeccion11(Request $request)
    {
        return $this->avanzarLeccion($request, 3, 1);
    }


    public function avanzarLeccion12(Request $request)
    {
        return $this->avanzarLeccion($request, 3, 2);
    }

    public function avanzarLeccion13(Request $request)
    {
        return $this->avanzarLeccion($request, 3, 3);
    }

    public function avanzarLeccion14(Request $request)
    {
        return $this->avanzarLeccion($request, 4, 1);
    }
    public function avanzarLeccion15(Request $request)
    {
        return $this->avanzarLeccion($request, 4, 2);
    }

    public function avanzarLeccion16(Request $request)
    {
        return $this->avanzarLeccion($request, 4, 3);
    }

    public function avanzarLeccion17(Request $request)
    {
        return $this->avanzarLeccion($request, 4, 4);
    }


    public function avanzarLeccion18(Request $request)
    {
        return $this->avanzarLeccion($request, 4, 5);
    }


    public function avanzarLeccion19(Request $request)
    {
        return $this->avanzarLeccion($request, 5, 1);
    }

    public function avanzarLeccion20(Request $request)
    {
        return $this->avanzarLeccion($request, 5, 2);
    }

    public function avanzarLeccion21(Request $request)
    {
        return $this->avanzarLeccion($request, 5, 3);
    }

    public function avanzarLeccion22(Request $request)
    {
        return $this->avanzarLeccion($request, 5, 4);
    }

    public function avanzarLeccion23(Request $request)
    {
        return $this->avanzarLeccion($request, 6, 1);
    }

    public function avanzarLeccion24(Request $request)
    {
        return $this->avanzarLeccion($request, 6, 2);
    }

    public function avanzarLeccion25(Request $request)
    {
        return $this->avanzarLeccion($request, 6, 3);
    }
}
