<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Leccion extends Model
{
    use HasFactory;

    protected $table = 'lecciones'; // 👈 aquí le dices el nombre exacto

    protected $fillable = [
        'nivel_id',
        'titulo',
        'orden',
    ];

    public function nivel()
    {
        return $this->belongsTo(Nivel::class);
    }
}
