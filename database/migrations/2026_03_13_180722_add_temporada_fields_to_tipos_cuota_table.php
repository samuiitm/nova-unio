<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tipos_cuota', function (Blueprint $table) {
            if (!Schema::hasColumn('tipos_cuota', 'tipo_vigencia')) {
                $table->string('tipo_vigencia', 20)
                    ->default('meses')
                    ->after('importe');
            }

            if (!Schema::hasColumn('tipos_cuota', 'venta_inicio_mes')) {
                $table->unsignedTinyInteger('venta_inicio_mes')
                    ->nullable()
                    ->after('duracion_meses');
            }

            if (!Schema::hasColumn('tipos_cuota', 'venta_fin_mes')) {
                $table->unsignedTinyInteger('venta_fin_mes')
                    ->nullable()
                    ->after('venta_inicio_mes');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tipos_cuota', function (Blueprint $table) {
            $columnas = Schema::getColumnListing('tipos_cuota');

            if (in_array('venta_fin_mes', $columnas, true)) {
                $table->dropColumn('venta_fin_mes');
            }

            if (in_array('venta_inicio_mes', $columnas, true)) {
                $table->dropColumn('venta_inicio_mes');
            }

            if (in_array('tipo_vigencia', $columnas, true)) {
                $table->dropColumn('tipo_vigencia');
            }
        });
    }
};