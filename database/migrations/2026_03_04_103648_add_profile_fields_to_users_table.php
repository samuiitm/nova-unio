<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('apellidos', 120)->nullable()->after('nombre');
            $table->string('phone', 30)->nullable()->after('email')->index();
            $table->string('telefono', 255)->nullable()->after('phone')->index();
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['apellidos', 'phone', 'telefono']);
        });
    }
};