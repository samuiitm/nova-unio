<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alumnos', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 120);
            $table->string('apellidos', 180);

            $table->string('catsalut', 50)->nullable()->unique();

            $table->date('fecha_nacimiento')->nullable();

            // He puesto 120 porque en tu mensaje quedó cortado (VARCHAR(12...)
            $table->string('lugar_nacimiento', 120)->nullable();

            $table->string('dni', 25)->nullable()->unique();

            $table->string('direccion', 200)->nullable();
            $table->string('cp', 10)->nullable();
            $table->string('poblacion', 120)->nullable();

            $table->string('telefono', 30)->nullable()->index();
            $table->string('email', 190)->nullable()->unique();

            $table->boolean('activo')->default(true)->index();

            $table->date('fecha_baja')->nullable();
            $table->date('fecha_inicio_actividad')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alumnos');
    }
};