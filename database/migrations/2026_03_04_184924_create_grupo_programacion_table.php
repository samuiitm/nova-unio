<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('grupo_programacion', function (Blueprint $table) {
            $table->id();

            $table->foreignId('grupo_id')->constrained('grupos')->cascadeOnDelete();

            // 1 = lunes ... 7 = domingo
            $table->unsignedTinyInteger('dia_semana')->index();

            $table->time('hora_inicio');
            $table->time('hora_fin');

            $table->date('vigente_desde')->nullable();
            $table->date('vigente_hasta')->nullable();

            $table->timestamps();

            $table->index(['grupo_id', 'dia_semana']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('grupo_programacion');
    }
};