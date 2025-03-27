<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Progreso;
use App\Models\Leccion;
use App\Models\Nivel;

class ProgresoController extends Controller
{
    /**
     * Actualiza el progreso del usuario en la lección y recalcula el progreso en el nivel.
     */
    public function actualizarProgreso(Request $request)
    {
        $request->validate([
            'usuario_id' => 'required|exists:usuarios,id',
            'leccion_id' => 'required|exists:lecciones,id',
        ]);

        $usuarioId = $request->usuario_id;
        $leccionId = $request->leccion_id;

        // Obtener la lección y su nivel
        $leccion = Leccion::findOrFail($leccionId);
        $nivelId = $leccion->nivel_id;

        // Buscar el progreso existente del usuario en este nivel
        $progreso = Progreso::where('usuario_id', $usuarioId)
            ->where('nivel_id', $nivelId)
            ->first();

        // Si no existe el progreso, crear uno nuevo para el primer nivel
        if (!$progreso) {
            $progreso = Progreso::create([
                'usuario_id' => $usuarioId,
                'nivel_id' => $nivelId,
                'leccion_id' => $leccionId,
                'porcentaje' => $this->calcularProgresoNivel($usuarioId, $nivelId),
            ]);
        } else {
            // Si existe, actualizamos el progreso en el nivel
            $progreso->update([
                'leccion_id' => $leccionId,
                'porcentaje' => $this->calcularProgresoNivel($usuarioId, $nivelId),
            ]);
        }

        return response()->json([
            'message' => 'Progreso actualizado correctamente',
            'progreso' => $progreso
        ]);
    }

    /**
     * Calcula el porcentaje de progreso en un nivel basado en las lecciones completadas.
     */
    private function calcularProgresoNivel($usuarioId, $nivelId)
    {
        // Contamos cuántas lecciones hay en este nivel
        $totalLecciones = Leccion::where('nivel_id', $nivelId)->count();

        // Contamos cuántas lecciones ha completado el usuario en este nivel
        $leccionesCompletadas = Progreso::where('usuario_id', $usuarioId)
            ->where('nivel_id', $nivelId)
            ->count();

        // Calculamos el porcentaje de avance
        return ($totalLecciones > 0) ? ($leccionesCompletadas / $totalLecciones) * 100 : 0;
    }
}

