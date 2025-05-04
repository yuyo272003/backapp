<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nivel extends Model
{
    use HasFactory;

    protected $table = 'niveles';

    protected $fillable = [
        'nombre',
        'numero_lecciones',
    ];

    public function lecciones()
    {
        return $this->hasMany(Leccion::class);
    }
}
