<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('cuota_id')
                ->constrained('cuotas')
                ->cascadeOnDelete();

            $table->foreignId('alumno_id')
                ->constrained('alumnos')
                ->cascadeOnDelete();

            $table->date('fecha_pago');
            $table->decimal('importe', 8, 2);

            $table->enum('metodo', ['efectivo', 'bizum', 'tarjeta', 'transferencia', 'otro'])->default('efectivo');
            $table->string('notas', 255)->nullable();

            $table->timestamps();

            // 1 pago por cuota (simple)
            $table->unique('cuota_id');

            $table->index(['fecha_pago']);
            $table->index(['alumno_id', 'fecha_pago']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};