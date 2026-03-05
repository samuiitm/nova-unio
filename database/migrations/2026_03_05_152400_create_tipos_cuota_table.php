<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tipos_cuota', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 120);
            $table->decimal('importe', 8, 2);
            $table->unsignedTinyInteger('duracion_meses')->default(1);
            $table->boolean('activo')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tipos_cuota');
    }
};