<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function tableName(): string
    {
        if (Schema::hasTable('usuarios')) return 'usuarios';
        return 'users';
    }

    public function up(): void
    {
        $table = $this->tableName();

        Schema::table($table, function (Blueprint $t) use ($table) {
            if (!Schema::hasColumn($table, 'apellidos')) {
                $t->string('apellidos', 120)->nullable();
            }

            if (!Schema::hasColumn($table, 'telefono')) {
                $t->string('telefono', 30)->nullable()->index();
            }

            if (!Schema::hasColumn($table, 'foto_perfil')) {
                $t->string('foto_perfil', 255)->nullable()->index();
            }
        });

        if (Schema::hasColumn($table, 'phone') && Schema::hasColumn($table, 'telefono')) {
            Schema::table($table, function (Blueprint $t) {
                $t->dropColumn('phone');
            });
        }
    }

    public function down(): void
    {
        $table = $this->tableName();

        Schema::table($table, function (Blueprint $t) use ($table) {
            if (Schema::hasColumn($table, 'foto_perfil')) $t->dropColumn('foto_perfil');
        });
    }
};