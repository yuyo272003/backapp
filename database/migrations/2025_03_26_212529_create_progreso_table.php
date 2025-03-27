<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('progreso', function (Blueprint $table) {
            Schema::create('progreso', function (Blueprint $table) {
                $table->id();
                $table->foreignId('usuario_id')->constrained('usuarios')->onDelete('cascade');
                $table->foreignId('nivel_id')->constrained('niveles')->onDelete('cascade');
                $table->foreignId('leccion_id')->constrained('lecciones')->onDelete('cascade');
                $table->decimal('porcentaje', 5, 2);  // El progreso de la lección (porcentaje)
                $table->integer('niveles_completados')->default(0);  // Número de niveles completados
                $table->timestamps();  // Marca de tiempo para el progreso
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('progreso');
    }
};
