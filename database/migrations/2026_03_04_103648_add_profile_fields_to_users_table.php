<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->string('last_name', 120)->nullable()->after('nombre');
            $table->string('telefono', 30)->nullable()->after('email')->index();
            $table->string('avatar_path', 255)->nullable()->after('telefono')->index();
        });
    }

    public function down(): void
    {
        Schema::table('usuarios', function (Blueprint $table) {
            $table->dropColumn(['last_name', 'telefono', 'avatar_path']);
        });
    }
};