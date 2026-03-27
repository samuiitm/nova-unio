<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('seguros')) {
            return;
        }

        Schema::create('seguros', function (Blueprint $table) {
            $table->id();

            $table->foreignId('alumno_id')
                ->constrained('alumnos')
                ->cascadeOnDelete();

            $table->enum('tipo', ['consell_esportiu', 'federacio_catalana_lucha']);
            $table->decimal('importe', 8, 2);

            $table->date('fecha_pago');
            $table->date('fecha_inicio');
            $table->date('fecha_fin');

            $table->enum('metodo', ['efectivo', 'bizum', 'tarjeta', 'transferencia', 'otro'])
                ->default('efectivo');

            $table->text('notas')->nullable();

            $table->timestamps();

            $table->index('alumno_id');
            $table->index('tipo');
            $table->index('fecha_pago');
            $table->index('fecha_fin');
            $table->index(['alumno_id', 'fecha_fin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seguros');
    }
};