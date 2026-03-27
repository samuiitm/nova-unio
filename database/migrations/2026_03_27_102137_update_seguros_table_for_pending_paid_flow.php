<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seguros', function (Blueprint $table) {
            $table->enum('estado', ['pendiente', 'pagado'])
                ->default('pagado')
                ->after('importe');
        });

        DB::statement("ALTER TABLE seguros MODIFY fecha_pago date NULL");
        DB::statement("ALTER TABLE seguros MODIFY fecha_inicio date NULL");
        DB::statement("ALTER TABLE seguros MODIFY fecha_fin date NULL");
        DB::statement("ALTER TABLE seguros MODIFY metodo enum('efectivo','bizum','tarjeta','transferencia','otro') NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE seguros MODIFY fecha_pago date NOT NULL");
        DB::statement("ALTER TABLE seguros MODIFY fecha_inicio date NOT NULL");
        DB::statement("ALTER TABLE seguros MODIFY fecha_fin date NOT NULL");
        DB::statement("ALTER TABLE seguros MODIFY metodo enum('efectivo','bizum','tarjeta','transferencia','otro') NOT NULL DEFAULT 'efectivo'");

        Schema::table('seguros', function (Blueprint $table) {
            $table->dropColumn('estado');
        });
    }
};