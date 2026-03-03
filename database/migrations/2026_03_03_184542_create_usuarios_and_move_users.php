<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('usuarios')) {
            Schema::create('usuarios', function (Blueprint $table) {
                $table->id();
                $table->string('nombre');
                $table->string('email')->unique();

                // tu campo
                $table->string('password_hash');

                // tu campo
                $table->string('rol', 20)->default('entrenador')->index();

                // tu campo
                $table->boolean('activo')->default(true)->index();

                // necesarios para Laravel (no molestan)
                $table->timestamp('email_verified_at')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }
        
        if (Schema::hasTable('users')) {
            $hasRole   = Schema::hasColumn('users', 'role');
            $hasActive = Schema::hasColumn('users', 'is_active');

            $rows = DB::table('users')->get();

            foreach ($rows as $row) {
                $exists = DB::table('usuarios')->where('email', $row->email)->exists();
                if ($exists) continue;

                DB::table('usuarios')->insert([
                    'id'              => $row->id,
                    'nombre'          => $row->name ?? 'Usuario',
                    'email'           => $row->email,
                    'password_hash'   => $row->password ?? '',
                    'rol'             => $hasRole ? ($row->role ?? 'entrenador') : 'admin',
                    'activo'          => $hasActive ? (bool) $row->is_active : true,
                    'email_verified_at'=> $row->email_verified_at ?? null,
                    'remember_token'  => $row->remember_token ?? null,
                    'created_at'      => $row->created_at ?? now(),
                    'updated_at'      => $row->updated_at ?? now(),
                ]);
            }

            Schema::drop('users');
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('usuarios');
    }
};