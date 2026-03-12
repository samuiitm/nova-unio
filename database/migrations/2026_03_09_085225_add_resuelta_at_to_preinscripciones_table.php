<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('preinscripciones', function (Blueprint $table) {
            $table->timestamp('resuelta_at')->nullable()->after('alumno_id');
            $table->index('alumno_id', 'preinscripciones_alumno_id_idx');
            $table->index('resuelta_at', 'preinscripciones_resuelta_at_idx');
        });
    }

    public function down(): void
    {
        Schema::table('preinscripciones', function (Blueprint $table) {
            $table->dropIndex('preinscripciones_alumno_id_idx');
            $table->dropIndex('preinscripciones_resuelta_at_idx');
            $table->dropColumn('resuelta_at');
        });
    }
};