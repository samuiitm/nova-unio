<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            // guardamos una foto del plan en el pago (por si luego se renueva y cambia la cuota)
            $table->foreignId('tipo_cuota_id')->nullable()->after('cuota_id')->constrained('tipos_cuota');
            $table->string('tipo_cuota_nombre', 120)->nullable()->after('tipo_cuota_id');

            // guardamos el periodo que se pagó
            $table->date('vigencia_inicio')->nullable()->after('fecha_pago');
            $table->date('vigencia_fin')->nullable()->after('vigencia_inicio');
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('tipo_cuota_id');
            $table->dropColumn(['tipo_cuota_nombre', 'vigencia_inicio', 'vigencia_fin']);
        });
    }
};