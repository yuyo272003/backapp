<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Progreso extends Model
{
    use HasFactory;

    protected $table = 'progreso'; // 👈 Nombre correcto de la tabla

    protected $fillable = [
        'usuario_id',
        'nivel_id',
        'leccion_id',
        'porcentaje',
        'niveles_completados',
    ];

    public function usuario()
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function leccion()
    {
        return $this->belongsTo(Leccion::class, 'leccion_id');
    }

    public function nivel()
    {
        return $this->belongsTo(Nivel::class, 'nivel_id');
    }
}
