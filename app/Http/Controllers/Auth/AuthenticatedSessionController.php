<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string'],
        ]);

        $user = \App\Models\User::where('name', $request->name)->first();

        if (!$user) {
            return response()->json([
                'message' => 'Usuario no encontrado',
            ], 422);
        }

        // Inicia sesión
       auth()->login($user);

        // Crea token de acceso
        $token = $user->createToken('API Token')->plainTextToken;
        $ultimoProgreso = \App\Models\Progreso::where('usuario_id', $user->id)
            ->orderByDesc('id') // o created_at
            ->first();

        $nivelesCompletados = $ultimoProgreso?->niveles_completados ?? 0;
;

        return response()->json([
            'user' => $user,
            'token' => $token,
            'niveles_completados' => $nivelesCompletados,
        ]);

    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
//        Auth::guard('web')->logout();
//
//        $request->session()->invalidate();
//
//        $request->session()->regenerateToken();

        //$request->user()->currentAccessToken()->delete();
        $request->user()->tokens()->delete();


        return response()->json([
            'success' => true,
            'message' => "Sesion cerrada"
        ],201);
    }
}
