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

    public function avanzar(Request $request)
    {
        $user = $request->user();

        // 1) Obtener el progreso más avanzado
        $progreso = Progreso::where('usuario_id', $user->id)
            ->orderByDesc('nivel_id')
            ->orderByDesc('leccion_id')
            ->first();

        $nivelActual   = $progreso->nivel_id ?? 1;
        $leccionActual = $progreso->leccion_id ?? 1;

        // 2) Saber el orden de la lección actual
        $ordenActual = Leccion::where('nivel_id', $nivelActual)
            ->where('id', $leccionActual)
            ->value('orden') ?? 1;

        // 3) Buscar la siguiente lección dentro del mismo nivel
        $siguiente = Leccion::where('nivel_id', $nivelActual)
            ->where('orden', '>', $ordenActual)
            ->orderBy('orden')
            ->first();

        // 4) Si ya no hay más lecciones en este nivel, pasar al siguiente nivel
        if (! $siguiente) {
            $nivelActual++;
            $siguiente = Leccion::where('nivel_id', $nivelActual)
                ->orderBy('orden')
                ->first();
        }

        // 5) Si tampoco hay nivel siguiente, terminar
        if (! $siguiente) {
            return response()->json([
                'message' => 'Ya completaste todo el contenido.',
            ], 200);
        }

        // 6) Calcular porcentaje basándonos en el ORDEN de la nueva lección
        $totalLecciones = Leccion::where('nivel_id', $siguiente->nivel_id)->count();
        $ordenSig       = $siguiente->orden;
        // … justo después de obtener $ordenSig y $totalLecciones
        $porcentaje = round((($ordenSig - 1) / $totalLecciones) * 100, 2);


        // 7) Actualizar (o crear si no existiera) el progreso de este nivel
        $registro = Progreso::updateOrCreate(
            [
                'usuario_id' => $user->id,
                'nivel_id'   => $siguiente->nivel_id,
            ],
            [
                'leccion_id' => $siguiente->id,
                'porcentaje' => $porcentaje,
            ]
        );

        return response()->json([
            'message'     => 'Progreso actualizado correctamente',
            'nivel_id'    => $siguiente->nivel_id,
            'leccion_id'  => $siguiente->id,
            'porcentaje'  => $porcentaje,
        ], 200);
    }



}
