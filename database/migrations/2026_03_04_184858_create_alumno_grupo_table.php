<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumno_grupo', function (Blueprint $table) {
            $table->id();

            $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
            $table->foreignId('grupo_id')->constrained('grupos')->cascadeOnDelete();

            $table->date('fecha_alta')->nullable();
            $table->date('fecha_baja')->nullable();

            $table->timestamps();

            $table->index(['grupo_id', 'fecha_baja']);
            $table->index(['alumno_id', 'fecha_baja']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumno_grupo');
    }
};