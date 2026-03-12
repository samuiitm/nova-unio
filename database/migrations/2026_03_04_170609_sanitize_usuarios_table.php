<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1) Asegurar que existe la tabla usuarios (por si algún entorno está raro)
        if (!Schema::hasTable('usuarios')) {
            Schema::create('usuarios', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('apellidos', 120)->nullable();
                $table->string('email')->unique();

                $table->string('telefono', 30)->nullable()->index();
                $table->string('foto_perfil', 255)->nullable()->index();

                $table->string('password_hash');

                $table->string('rol', 20)->default('entrenador')->index();
                $table->boolean('activo')->default(true)->index();

                $table->timestamp('email_verified_at')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        // 2) Asegurar columnas finales (si la tabla ya existía con mezcla)
        Schema::table('usuarios', function (Blueprint $t) {
            if (!Schema::hasColumn('usuarios', 'apellidos')) {
                $t->string('apellidos', 120)->nullable()->after('nombre');
            }

            if (!Schema::hasColumn('usuarios', 'telefono')) {
                $t->string('telefono', 30)->nullable()->after('email')->index();
            }

            if (!Schema::hasColumn('usuarios', 'foto_perfil')) {
                $t->string('foto_perfil', 255)->nullable()->after('telefono')->index();
            }

            if (!Schema::hasColumn('usuarios', 'password_hash')) {
                $t->string('password_hash');
            }

            if (!Schema::hasColumn('usuarios', 'rol')) {
                $t->string('rol', 20)->default('entrenador')->index();
            }

            if (!Schema::hasColumn('usuarios', 'activo')) {
                $t->boolean('activo')->default(true)->index();
            }

            // columnas “Laravel” por si faltan
            if (!Schema::hasColumn('usuarios', 'email_verified_at')) {
                $t->timestamp('email_verified_at')->nullable();
            }

            if (!Schema::hasColumn('usuarios', 'remember_token')) {
                $t->rememberToken();
            }
        });

        // 3) Migrar datos de columnas legacy -> columnas finales
        // last_name -> apellidos
        if (Schema::hasColumn('usuarios', 'last_name')) {
            DB::statement("
                UPDATE usuarios
                SET apellidos = last_name
                WHERE (apellidos IS NULL OR apellidos = '')
                  AND last_name IS NOT NULL
                  AND last_name <> ''
            ");
        }

        // avatar_path -> foto_perfil
        if (Schema::hasColumn('usuarios', 'avatar_path')) {
            DB::statement("
                UPDATE usuarios
                SET foto_perfil = avatar_path
                WHERE (foto_perfil IS NULL OR foto_perfil = '')
                  AND avatar_path IS NOT NULL
                  AND avatar_path <> ''
            ");
        }

        // role/is_active (si por algún motivo existen en usuarios)
        if (Schema::hasColumn('usuarios', 'role')) {
            DB::statement("
                UPDATE usuarios
                SET rol = role
                WHERE (rol IS NULL OR rol = '')
                  AND role IS NOT NULL
                  AND role <> ''
            ");
        }

        if (Schema::hasColumn('usuarios', 'is_active')) {
            DB::statement("
                UPDATE usuarios
                SET activo = is_active
                WHERE activo IS NULL
            ");
        }

        // 4) Normalizar valores por defecto si vienen vacíos
        DB::statement("UPDATE usuarios SET rol = 'entrenador' WHERE rol IS NULL OR rol = ''");
        DB::statement("UPDATE usuarios SET activo = 1 WHERE activo IS NULL");

        // 5) Eliminar columnas legacy si existen (limpieza final)
        Schema::table('usuarios', function (Blueprint $t) {
            if (Schema::hasColumn('usuarios', 'last_name')) {
                $t->dropColumn('last_name');
            }
            if (Schema::hasColumn('usuarios', 'avatar_path')) {
                $t->dropColumn('avatar_path');
            }
            if (Schema::hasColumn('usuarios', 'phone')) {
                $t->dropColumn('phone');
            }
            if (Schema::hasColumn('usuarios', 'role')) {
                $t->dropColumn('role');
            }
            if (Schema::hasColumn('usuarios', 'is_active')) {
                $t->dropColumn('is_active');
            }
        });
    }

    public function down(): void
    {
        // No hacemos rollback automático porque esto es una migración de saneamiento.
    }
};