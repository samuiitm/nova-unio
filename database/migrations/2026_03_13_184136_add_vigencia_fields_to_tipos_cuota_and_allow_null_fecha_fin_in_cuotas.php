<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('tipos_cuota', 'tipo_vigencia')) {
            Schema::table('tipos_cuota', function (Blueprint $table) {
                $table->string('tipo_vigencia', 20)
                    ->default('meses')
                    ->after('importe');
            });
        }

        if (!Schema::hasColumn('tipos_cuota', 'venta_inicio_mes')) {
            Schema::table('tipos_cuota', function (Blueprint $table) {
                $table->unsignedTinyInteger('venta_inicio_mes')
                    ->nullable()
                    ->after('duracion_meses');
            });
        }

        if (!Schema::hasColumn('tipos_cuota', 'venta_fin_mes')) {
            Schema::table('tipos_cuota', function (Blueprint $table) {
                $table->unsignedTinyInteger('venta_fin_mes')
                    ->nullable()
                    ->after('venta_inicio_mes');
            });
        }

        // Para becas / cuotas indefinidas
        DB::statement('ALTER TABLE cuotas MODIFY fecha_fin DATE NULL');
    }

    public function down(): void
    {
        if (Schema::hasColumn('tipos_cuota', 'venta_fin_mes')) {
            Schema::table('tipos_cuota', function (Blueprint $table) {
                $table->dropColumn('venta_fin_mes');
            });
        }

        if (Schema::hasColumn('tipos_cuota', 'venta_inicio_mes')) {
            Schema::table('tipos_cuota', function (Blueprint $table) {
                $table->dropColumn('venta_inicio_mes');
            });
        }

        if (Schema::hasColumn('tipos_cuota', 'tipo_vigencia')) {
            Schema::table('tipos_cuota', function (Blueprint $table) {
                $table->dropColumn('tipo_vigencia');
            });
        }

        // Si hubiera cuotas indefinidas, les ponemos fecha_fin = fecha_inicio antes de volver a NOT NULL
        DB::statement('UPDATE cuotas SET fecha_fin = fecha_inicio WHERE fecha_fin IS NULL');
        DB::statement('ALTER TABLE cuotas MODIFY fecha_fin DATE NOT NULL');
    }
};