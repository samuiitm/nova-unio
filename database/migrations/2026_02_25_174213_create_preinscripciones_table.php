<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('preinscripciones', function (Blueprint $table) {
            $table->id();

            $table->string('nombre', 80);
            $table->string('apellidos', 120)->nullable();
            $table->string('email', 120);
            $table->string('telefono', 30)->nullable();
            $table->unsignedTinyInteger('edad')->nullable();

            $table->string('modalidad', 30); // Sambo Kids / MMA-Sambo / etc
            $table->string('nivel', 30)->nullable();    // Principiante / Intermedio / Avanzado
            $table->string('objetivo', 30)->nullable(); // Aprender / Ponerme en forma / Competir
            $table->text('mensaje')->nullable();

            $table->string('estado', 20)->default('nueva'); // nueva/en_proceso/aceptada/rechazada
            $table->unsignedBigInteger('alumno_id')->nullable();

            $table->timestamps();

            $table->index('estado');
            $table->index('email');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('preinscripciones');
    }
};