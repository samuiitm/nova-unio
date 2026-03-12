<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('clases', function (Blueprint $table) {
            $table->id();

            $table->foreignId('grupo_id')
                  ->constrained()
                  ->onDelete('cascade');

            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');

            $table->enum('estado', ['programada', 'cancelada'])
                  ->default('programada');

            $table->boolean('asistencia_cerrada')
                  ->default(false);

            $table->timestamps();

            $table->unique(['grupo_id', 'fecha', 'hora_inicio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clases');
    }
};