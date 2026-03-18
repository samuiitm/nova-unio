<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('alumnos', function (Blueprint $table) {
            if (!Schema::hasColumn('alumnos', 'tutor_legal_nombre')) {
                $table->string('tutor_legal_nombre', 180)
                    ->nullable()
                    ->after('email');
            }

            if (!Schema::hasColumn('alumnos', 'tutor_legal_dni')) {
                $table->string('tutor_legal_dni', 25)
                    ->nullable()
                    ->after('tutor_legal_nombre');
            }

            if (!Schema::hasColumn('alumnos', 'tutor_legal_relacion')) {
                $table->enum('tutor_legal_relacion', ['padre', 'madre', 'tutor'])
                    ->nullable()
                    ->after('tutor_legal_dni');
            }
        });

        if (!Schema::hasTable('alumno_telefonos_contacto')) {
            Schema::create('alumno_telefonos_contacto', function (Blueprint $table) {
                $table->id();
                $table->foreignId('alumno_id')->constrained('alumnos')->cascadeOnDelete();
                $table->string('contacto', 120);
                $table->string('telefono', 30);
                $table->unsignedSmallInteger('orden')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('alumno_telefonos_contacto')) {
            Schema::dropIfExists('alumno_telefonos_contacto');
        }

        if (Schema::hasColumn('alumnos', 'tutor_legal_relacion')) {
            Schema::table('alumnos', function (Blueprint $table) {
                $table->dropColumn('tutor_legal_relacion');
            });
        }

        if (Schema::hasColumn('alumnos', 'tutor_legal_dni')) {
            Schema::table('alumnos', function (Blueprint $table) {
                $table->dropColumn('tutor_legal_dni');
            });
        }

        if (Schema::hasColumn('alumnos', 'tutor_legal_nombre')) {
            Schema::table('alumnos', function (Blueprint $table) {
                $table->dropColumn('tutor_legal_nombre');
            });
        }
    }
};