<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1) Quitamos la FK que usa el índice de cuota_id
        Schema::table('pagos', function (Blueprint $table) {
            // nombre típico: pagos_cuota_id_foreign
            // si te da error de nombre, mira el paso 2 de abajo
            $table->dropForeign(['cuota_id']);
        });

        // 2) Quitamos el UNIQUE para permitir varios pagos por cuota
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropUnique('pagos_cuota_id_unique');
        });

        // 3) Dejamos un índice normal (no único)
        Schema::table('pagos', function (Blueprint $table) {
            $table->index('cuota_id');
        });

        // 4) Volvemos a poner la FK
        Schema::table('pagos', function (Blueprint $table) {
            $table->foreign('cuota_id')
                ->references('id')
                ->on('cuotas')
                ->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        // revertimos: quitamos FK, quitamos index, ponemos unique, ponemos FK
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropForeign(['cuota_id']);
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->dropIndex(['cuota_id']);
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->unique('cuota_id');
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->foreign('cuota_id')
                ->references('id')
                ->on('cuotas')
                ->cascadeOnDelete();
        });
    }
};