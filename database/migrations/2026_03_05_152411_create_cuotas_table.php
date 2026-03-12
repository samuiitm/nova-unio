<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cuotas', function (Blueprint $table) {
            $table->id();

            $table->foreignId('alumno_id')
                ->constrained('alumnos')
                ->cascadeOnDelete();

            $table->foreignId('tipo_cuota_id')
                ->nullable()
                ->constrained('tipos_cuota')
                ->nullOnDelete();

            $table->date('fecha_inicio');
            $table->date('fecha_fin');

            $table->decimal('importe', 8, 2);

            $table->enum('estado', ['pendiente', 'pagada', 'anulada'])->default('pendiente');

            $table->timestamps();

            $table->index(['alumno_id', 'fecha_inicio']);
            $table->index(['estado', 'fecha_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cuotas');
    }
};