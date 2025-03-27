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
        Schema::create('lecciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nivel_id')->constrained()->onDelete('cascade');  // Relación con niveles
            $table->string('titulo');  // Título de la lección
            $table->integer('orden');  // Orden de la lección en el nivel
            $table->timestamps();  // Marca de tiempo para la lección
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lecciones');
    }
};
