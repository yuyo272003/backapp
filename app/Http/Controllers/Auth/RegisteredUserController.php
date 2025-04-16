<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Leccion;
use App\Models\Progreso;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;

class RegisteredUserController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255','unique:'.User::class],
            'email' => ['nullable', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['nullable', /*'confirmed', Rules\Password::defaults()*/],
        ]);

        $user = User::create([
            'name' => $request->name,
//            'email' => $request->email,
//           'password' => Hash::make($request->string('password')),
        ]);


        $primeraLeccion = Leccion::orderBy('id')->first();

        if ($primeraLeccion) {
            Progreso::create([
                'usuario_id' => $user->id,
                'leccion_id' => $primeraLeccion->id,
                'nivel_id' => $primeraLeccion->nivel_id,
                'porcentaje' => 0,
                'niveles_completados' => 0,
            ]);
        }



        return response()->json([
            'user' => $user,
            'token' => $user->createToken('API Token')->plainTextToken,

        ], 201);
    }
}
