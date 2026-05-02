<?php

namespace Database\Seeders;

use App\Models\Alumno;
use App\Models\AlumnoTelefonoContacto;
use App\Models\Asistencia;
use App\Models\Clase;
use App\Models\Cuota;
use App\Models\Grupo;
use App\Models\GrupoProgramacion;
use App\Models\Pago;
use App\Models\Preinscripcion;
use App\Models\Seguro;
use App\Models\TipoCuota;
use App\Services\CalculadorVigenciaCuotaService;
use App\Services\CalculadorVigenciaSeguroService;
use App\Services\GeneradorClasesService;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        $hoy = now()->startOfDay();

        $grupoMma = Grupo::updateOrCreate(
            ['nombre' => 'MMA Adultos'],
            ['color' => '#7C5CFF', 'activo' => true]
        );

        $grupoSambo = Grupo::updateOrCreate(
            ['nombre' => 'Sambo Competición'],
            ['color' => '#16A34A', 'activo' => true]
        );

        $grupoInfantil = Grupo::updateOrCreate(
            ['nombre' => 'MMA Infantil'],
            ['color' => '#F59E0B', 'activo' => true]
        );

        $this->crearProgramacion($grupoMma, 1, '19:00:00', '20:30:00', $hoy->copy()->subMonths(2)->toDateString());
        $this->crearProgramacion($grupoMma, 3, '19:00:00', '20:30:00', $hoy->copy()->subMonths(2)->toDateString());

        $this->crearProgramacion($grupoSambo, 2, '20:00:00', '21:30:00', $hoy->copy()->subMonths(2)->toDateString());
        $this->crearProgramacion($grupoSambo, 4, '20:00:00', '21:30:00', $hoy->copy()->subMonths(2)->toDateString());

        $this->crearProgramacion($grupoInfantil, 2, '18:00:00', '19:00:00', $hoy->copy()->subMonths(2)->toDateString());
        $this->crearProgramacion($grupoInfantil, 4, '18:00:00', '19:00:00', $hoy->copy()->subMonths(2)->toDateString());

        $alumno1 = Alumno::updateOrCreate(
            ['dni' => 'DEMO0001A'],
            [
                'nombre' => 'Marc',
                'apellidos' => 'Serrano Puig',
                'catsalut' => 'CATSALUT-DEMO-0001',
                'fecha_nacimiento' => '2003-05-14',
                'lugar_nacimiento' => 'Lloret de Mar',
                'direccion' => 'Carrer Demo 1',
                'cp' => '17310',
                'poblacion' => 'Lloret de Mar',
                'telefono' => '611111111',
                'email' => 'marc.demo@novaunio.local',
                'activo' => true,
                'fecha_inicio_actividad' => $hoy->copy()->subMonths(4)->toDateString(),
            ]
        );

        $alumno2 = Alumno::updateOrCreate(
            ['dni' => 'DEMO0002B'],
            [
                'nombre' => 'Laia',
                'apellidos' => 'Casas Riera',
                'catsalut' => 'CATSALUT-DEMO-0002',
                'fecha_nacimiento' => '2008-11-02',
                'lugar_nacimiento' => 'Blanes',
                'direccion' => 'Avinguda Demo 2',
                'cp' => '17310',
                'poblacion' => 'Lloret de Mar',
                'telefono' => '622222222',
                'email' => 'laia.demo@novaunio.local',
                'tutor_legal_nombre' => 'Anna Riera',
                'tutor_legal_dni' => 'TUTOR0002X',
                'tutor_legal_relacion' => 'madre',
                'activo' => true,
                'fecha_inicio_actividad' => $hoy->copy()->subMonths(3)->toDateString(),
            ]
        );

        $alumno3 = Alumno::updateOrCreate(
            ['dni' => 'DEMO0003C'],
            [
                'nombre' => 'Joel',
                'apellidos' => 'Martín Costa',
                'catsalut' => 'CATSALUT-DEMO-0003',
                'fecha_nacimiento' => '1999-03-20',
                'lugar_nacimiento' => 'Tossa de Mar',
                'direccion' => 'Passeig Demo 3',
                'cp' => '17310',
                'poblacion' => 'Lloret de Mar',
                'telefono' => '633333333',
                'email' => 'joel.demo@novaunio.local',
                'activo' => true,
                'fecha_inicio_actividad' => $hoy->copy()->subMonths(6)->toDateString(),
            ]
        );

        $alumno4 = Alumno::updateOrCreate(
            ['dni' => 'DEMO0004D'],
            [
                'nombre' => 'Nora',
                'apellidos' => 'Vidal Soler',
                'catsalut' => 'CATSALUT-DEMO-0004',
                'fecha_nacimiento' => '2012-07-09',
                'lugar_nacimiento' => 'Lloret de Mar',
                'direccion' => 'Ronda Demo 4',
                'cp' => '17310',
                'poblacion' => 'Lloret de Mar',
                'telefono' => '644444444',
                'email' => 'nora.demo@novaunio.local',
                'tutor_legal_nombre' => 'David Soler',
                'tutor_legal_dni' => 'TUTOR0004Y',
                'tutor_legal_relacion' => 'padre',
                'activo' => true,
                'fecha_inicio_actividad' => $hoy->copy()->subMonths(2)->toDateString(),
            ]
        );

        AlumnoTelefonoContacto::updateOrCreate(
            [
                'alumno_id' => $alumno2->id,
                'contacto' => 'Padre',
            ],
            [
                'telefono' => '655555555',
                'orden' => 1,
            ]
        );

        AlumnoTelefonoContacto::updateOrCreate(
            [
                'alumno_id' => $alumno4->id,
                'contacto' => 'Madre',
            ],
            [
                'telefono' => '666666666',
                'orden' => 1,
            ]
        );

        $this->asignarAlumnoAGrupo($alumno1, $grupoMma, $hoy->copy()->subMonths(4)->toDateString());
        $this->asignarAlumnoAGrupo($alumno2, $grupoInfantil, $hoy->copy()->subMonths(3)->toDateString());
        $this->asignarAlumnoAGrupo($alumno3, $grupoSambo, $hoy->copy()->subMonths(6)->toDateString());
        $this->asignarAlumnoAGrupo($alumno4, $grupoInfantil, $hoy->copy()->subMonths(2)->toDateString());

        $tipoMensual = TipoCuota::updateOrCreate(
            ['nombre' => 'Mensual'],
            [
                'importe' => 60,
                'tipo_vigencia' => 'meses',
                'duracion_meses' => 1,
                'activo' => true,
            ]
        );

        $tipoTrimestral = TipoCuota::updateOrCreate(
            ['nombre' => 'Trimestral'],
            [
                'importe' => 150,
                'tipo_vigencia' => 'meses',
                'duracion_meses' => 3,
                'activo' => true,
            ]
        );

        $tipoTemporada = TipoCuota::updateOrCreate(
            ['nombre' => 'Temporada'],
            [
                'importe' => 420,
                'tipo_vigencia' => 'temporada',
                'duracion_meses' => 1,
                'venta_inicio_mes' => 8,
                'venta_fin_mes' => 12,
                'activo' => true,
            ]
        );

        $tipoBeca = TipoCuota::updateOrCreate(
            ['nombre' => 'Beca'],
            [
                'importe' => 0,
                'tipo_vigencia' => 'indefinida',
                'duracion_meses' => 1,
                'activo' => true,
            ]
        );

        $calculadorCuota = app(CalculadorVigenciaCuotaService::class);

        $fechaPagoMarc = $hoy->copy()->subDays(10);
        $vigenciaMarc = $calculadorCuota->calcularParaPago($tipoMensual, $fechaPagoMarc);
        $cuotaMarc = Cuota::updateOrCreate(
            [
                'alumno_id' => $alumno1->id,
                'tipo_cuota_id' => $tipoMensual->id,
                'estado' => 'pagada',
                'fecha_inicio' => $vigenciaMarc['inicio']->toDateString(),
            ],
            [
                'fecha_fin' => $vigenciaMarc['fin']?->toDateString(),
                'importe' => $tipoMensual->importe,
            ]
        );

        Pago::updateOrCreate(
            [
                'cuota_id' => $cuotaMarc->id,
                'alumno_id' => $alumno1->id,
                'fecha_pago' => $fechaPagoMarc->toDateString(),
            ],
            [
                'importe' => $tipoMensual->importe,
                'metodo' => 'bizum',
                'notas' => strtoupper($fechaPagoMarc->locale('es')->translatedFormat('M Y')),
                'tipo_cuota_id' => $tipoMensual->id,
                'tipo_cuota_nombre' => $tipoMensual->nombre,
                'vigencia_inicio' => $vigenciaMarc['inicio']->toDateString(),
                'vigencia_fin' => $vigenciaMarc['fin']?->toDateString(),
            ]
        );

        Cuota::updateOrCreate(
            [
                'alumno_id' => $alumno2->id,
                'tipo_cuota_id' => $tipoTrimestral->id,
                'estado' => 'pendiente',
            ],
            [
                'fecha_inicio' => null,
                'fecha_fin' => null,
                'importe' => $tipoTrimestral->importe,
            ]
        );

        $fechaPagoJoel = $hoy->copy()->subDays(75);
        $vigenciaJoel = $calculadorCuota->calcularParaPago($tipoMensual, $fechaPagoJoel);
        $cuotaJoel = Cuota::updateOrCreate(
            [
                'alumno_id' => $alumno3->id,
                'tipo_cuota_id' => $tipoMensual->id,
                'estado' => 'pagada',
                'fecha_inicio' => $vigenciaJoel['inicio']->toDateString(),
            ],
            [
                'fecha_fin' => $vigenciaJoel['fin']?->toDateString(),
                'importe' => $tipoMensual->importe,
            ]
        );

        Pago::updateOrCreate(
            [
                'cuota_id' => $cuotaJoel->id,
                'alumno_id' => $alumno3->id,
                'fecha_pago' => $fechaPagoJoel->toDateString(),
            ],
            [
                'importe' => $tipoMensual->importe,
                'metodo' => 'efectivo',
                'notas' => strtoupper($fechaPagoJoel->locale('es')->translatedFormat('M Y')),
                'tipo_cuota_id' => $tipoMensual->id,
                'tipo_cuota_nombre' => $tipoMensual->nombre,
                'vigencia_inicio' => $vigenciaJoel['inicio']->toDateString(),
                'vigencia_fin' => $vigenciaJoel['fin']?->toDateString(),
            ]
        );

        $vigenciaBeca = $calculadorCuota->calcularParaPago($tipoBeca, $hoy->copy()->subMonth());
        $cuotaNora = Cuota::updateOrCreate(
            [
                'alumno_id' => $alumno4->id,
                'tipo_cuota_id' => $tipoBeca->id,
                'estado' => 'pagada',
                'fecha_inicio' => $vigenciaBeca['inicio']->toDateString(),
            ],
            [
                'fecha_fin' => null,
                'importe' => $tipoBeca->importe,
            ]
        );

        Pago::updateOrCreate(
            [
                'cuota_id' => $cuotaNora->id,
                'alumno_id' => $alumno4->id,
                'fecha_pago' => $hoy->copy()->subMonth()->toDateString(),
            ],
            [
                'importe' => $tipoBeca->importe,
                'metodo' => 'otro',
                'notas' => 'BECA',
                'tipo_cuota_id' => $tipoBeca->id,
                'tipo_cuota_nombre' => $tipoBeca->nombre,
                'vigencia_inicio' => $vigenciaBeca['inicio']->toDateString(),
                'vigencia_fin' => null,
            ]
        );

        $calculadorSeguro = app(CalculadorVigenciaSeguroService::class);

        $seguroMarc = $calculadorSeguro->calcularVigencia('consell_esportiu', $hoy->copy()->subDays(20));
        Seguro::updateOrCreate(
            [
                'alumno_id' => $alumno1->id,
                'tipo' => 'consell_esportiu',
                'fecha_pago' => $seguroMarc['inicio']->toDateString(),
            ],
            [
                'importe' => $seguroMarc['importe'],
                'estado' => 'pagado',
                'fecha_inicio' => $seguroMarc['inicio']->toDateString(),
                'fecha_fin' => $seguroMarc['fin']->toDateString(),
                'metodo' => 'transferencia',
                'notas' => 'Seguro demo vigente',
            ]
        );

        Seguro::updateOrCreate(
            [
                'alumno_id' => $alumno2->id,
                'tipo' => 'federacio_catalana_lucha',
                'estado' => 'pendiente',
            ],
            [
                'importe' => 75,
                'fecha_pago' => null,
                'fecha_inicio' => null,
                'fecha_fin' => null,
                'metodo' => null,
                'notas' => 'Pendiente de cobro',
            ]
        );

        Preinscripcion::updateOrCreate(
            ['email' => 'preinscripcion1@novaunio.local'],
            [
                'nombre' => 'Hugo',
                'apellidos' => 'Ramírez',
                'telefono' => '677111111',
                'edad' => 15,
                'modalidad' => 'MMA',
                'nivel' => 'iniciacion',
                'objetivo' => 'probar',
                'mensaje' => 'Quiero venir a probar una clase.',
                'estado' => 'nueva',
            ]
        );

        Preinscripcion::updateOrCreate(
            ['email' => 'preinscripcion2@novaunio.local'],
            [
                'nombre' => 'Claudia',
                'apellidos' => 'Morales',
                'telefono' => '677222222',
                'edad' => 24,
                'modalidad' => 'Sambo',
                'nivel' => 'medio',
                'objetivo' => 'competir',
                'mensaje' => 'Me interesa entrenar de forma regular.',
                'estado' => 'en_proceso',
            ]
        );

        $this->crearClaseConAsistencias(
            grupo: $grupoMma,
            fecha: $hoy->copy()->subDays(21),
            horaInicio: '19:00:00',
            horaFin: '20:30:00',
            asistencias: [
                [$alumno1->id, 'presente'],
            ],
        );

        $this->crearClaseConAsistencias(
            grupo: $grupoMma,
            fecha: $hoy->copy()->subDays(14),
            horaInicio: '19:00:00',
            horaFin: '20:30:00',
            asistencias: [
                [$alumno1->id, 'presente'],
            ],
        );

        $this->crearClaseConAsistencias(
            grupo: $grupoMma,
            fecha: $hoy->copy()->subDays(7),
            horaInicio: '19:00:00',
            horaFin: '20:30:00',
            asistencias: [],
        );

        $this->crearClaseConAsistencias(
            grupo: $grupoMma,
            fecha: $hoy->copy()->subDays(3),
            horaInicio: '19:00:00',
            horaFin: '20:30:00',
            asistencias: [
                [$alumno1->id, 'ausente'],
            ],
        );

        $this->crearClaseConAsistencias(
            grupo: $grupoInfantil,
            fecha: $hoy->copy()->subDays(10),
            horaInicio: '18:00:00',
            horaFin: '19:00:00',
            asistencias: [
                [$alumno2->id, 'presente'],
                [$alumno4->id, 'presente'],
            ],
        );

        $this->crearClaseConAsistencias(
            grupo: $grupoInfantil,
            fecha: $hoy->copy()->subDays(5),
            horaInicio: '18:00:00',
            horaFin: '19:00:00',
            asistencias: [],
        );

        $this->crearClaseConAsistencias(
            grupo: $grupoSambo,
            fecha: $hoy->copy()->subDays(8),
            horaInicio: '20:00:00',
            horaFin: '21:30:00',
            asistencias: [
                [$alumno3->id, 'presente'],
            ],
        );

        $this->crearClaseConAsistencias(
            grupo: $grupoSambo,
            fecha: $hoy->copy()->subDays(1),
            horaInicio: '20:00:00',
            horaFin: '21:30:00',
            asistencias: [],
            estado: 'cancelada',
        );

        $generador = app(GeneradorClasesService::class);
        $generador->generarParaGrupoTrasCambio($grupoMma, $hoy->copy());
        $generador->generarParaGrupoTrasCambio($grupoSambo, $hoy->copy());
        $generador->generarParaGrupoTrasCambio($grupoInfantil, $hoy->copy());
    }

    private function crearProgramacion(
        Grupo $grupo,
        int $diaSemana,
        string $horaInicio,
        string $horaFin,
        string $vigenteDesde
    ): void {
        GrupoProgramacion::updateOrCreate(
            [
                'grupo_id' => $grupo->id,
                'dia_semana' => $diaSemana,
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin,
            ],
            [
                'vigente_desde' => $vigenteDesde,
                'vigente_hasta' => null,
            ]
        );
    }

    private function asignarAlumnoAGrupo(Alumno $alumno, Grupo $grupo, string $fechaAlta): void
    {
        DB::table('alumno_grupo')->updateOrInsert(
            [
                'alumno_id' => $alumno->id,
                'grupo_id' => $grupo->id,
                'fecha_alta' => $fechaAlta,
            ],
            [
                'fecha_baja' => null,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );
    }

    private function crearClaseConAsistencias(
        Grupo $grupo,
        Carbon $fecha,
        string $horaInicio,
        string $horaFin,
        array $asistencias = [],
        string $estado = 'programada'
    ): void {
        $clase = Clase::updateOrCreate(
            [
                'grupo_id' => $grupo->id,
                'fecha' => $fecha->toDateString(),
                'hora_inicio' => $horaInicio,
                'hora_fin' => $horaFin,
            ],
            [
                'estado' => $estado,
                'asistencia_cerrada' => 0,
            ]
        );

        foreach ($asistencias as [$alumnoId, $estadoAsistencia]) {
            Asistencia::updateOrCreate(
                [
                    'clase_id' => $clase->id,
                    'alumno_id' => $alumnoId,
                ],
                [
                    'estado' => $estadoAsistencia,
                ]
            );
        }
    }
}