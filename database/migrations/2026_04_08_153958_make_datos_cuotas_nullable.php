<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE cuotas MODIFY fecha_inicio date NULL");
        DB::statement("ALTER TABLE cuotas MODIFY fecha_fin date NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE cuotas MODIFY fecha_inicio date NOT NULL");
        DB::statement("ALTER TABLE cuotas MODIFY fecha_fin date NOT NULL");
    }
};