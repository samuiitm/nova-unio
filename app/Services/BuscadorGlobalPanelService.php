<?php

namespace App\Services;

use App\Models\Alumno;
use App\Models\Grupo;
use App\Models\Preinscripcion;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class BuscadorGlobalPanelService
{
    public function buscar(?string $termino, ?User $usuario, bool $modoSugerencias = true): array
    {
        $termino = trim((string) $termino);
        $terminoNormalizado = $this->normalizar($termino);
        $terminos = $this->extraerTerminos($terminoNormalizado);

        if ($termino === '' || count($terminos) === 0) {
            return [
                'query' => $termino,
                'min_length' => 2,
                'total' => 0,
                'groups' => [],
            ];
        }

        if (mb_strlen($termino) < 2) {
            return [
                'query' => $termino,
                'min_length' => 2,
                'total' => 0,
                'groups' => [],
            ];
        }

        $limite = $modoSugerencias ? 5 : 12;

        $grupos = collect();

        $secciones = $this->buscarSecciones($terminoNormalizado, $usuario, $limite);
        if ($secciones->isNotEmpty()) {
            $grupos->push([
                'key' => 'secciones',
                'label' => 'Secciones',
                'items' => $secciones->values()->all(),
                'count' => $secciones->count(),
            ]);
        }

        if ($usuario?->puedeGestionarClub()) {
            $alumnos = $this->buscarAlumnos($terminos, $limite);
            if ($alumnos->isNotEmpty()) {
                $grupos->push([
                    'key' => 'alumnos',
                    'label' => 'Alumnos',
                    'items' => $alumnos->values()->all(),
                    'count' => $alumnos->count(),
                ]);
            }

            $gruposClub = $this->buscarGrupos($terminos, $limite);
            if ($gruposClub->isNotEmpty()) {
                $grupos->push([
                    'key' => 'grupos',
                    'label' => 'Grupos',
                    'items' => $gruposClub->values()->all(),
                    'count' => $gruposClub->count(),
                ]);
            }

            $preinscripciones = $this->buscarPreinscripciones($terminos, $limite);
            if ($preinscripciones->isNotEmpty()) {
                $grupos->push([
                    'key' => 'preinscripciones',
                    'label' => 'Preinscripciones',
                    'items' => $preinscripciones->values()->all(),
                    'count' => $preinscripciones->count(),
                ]);
            }
        }

        if ($usuario?->puedeGestionarUsuarios()) {
            $usuarios = $this->buscarUsuarios($terminos, $limite);
            if ($usuarios->isNotEmpty()) {
                $grupos->push([
                    'key' => 'usuarios',
                    'label' => 'Usuarios',
                    'items' => $usuarios->values()->all(),
                    'count' => $usuarios->count(),
                ]);
            }
        }

        return [
            'query' => $termino,
            'min_length' => 2,
            'total' => (int) $grupos->sum('count'),
            'groups' => $grupos->values()->all(),
        ];
    }

    protected function buscarSecciones(string $terminoNormalizado, ?User $usuario, int $limite): Collection
    {
        return collect($this->definirSecciones($usuario))
            ->map(function (array $item) use ($terminoNormalizado) {
                $haystack = $this->normalizar(implode(' ', array_filter([
                    $item['title'] ?? '',
                    $item['subtitle'] ?? '',
                    implode(' ', $item['keywords'] ?? []),
                ])));

                if (!Str::contains($haystack, $terminoNormalizado)) {
                    return null;
                }

                return [
                    'type' => 'seccion',
                    'title' => $item['title'],
                    'subtitle' => $item['subtitle'],
                    'url' => $item['url'],
                    'meta' => $item['meta'] ?? null,
                ];
            })
            ->filter()
            ->take($limite)
            ->values();
    }

    protected function buscarAlumnos(array $terminos, int $limite): Collection
    {
        return Alumno::query()
            ->select(['id', 'nombre', 'apellidos', 'dni', 'telefono', 'email', 'catsalut', 'activo', 'fecha_baja', 'foto_path'])
            ->when($terminos !== [], fn (Builder $query) => $this->aplicarBusquedaPorTerminos(
                $query,
                $terminos,
                ['nombre', 'apellidos', 'dni', 'telefono', 'email', 'catsalut']
            ))
            ->orderByDesc('activo')
            ->orderBy('apellidos')
            ->orderBy('nombre')
            ->limit($limite)
            ->get()
            ->map(function (Alumno $alumno) {
                $meta = [];

                if ($alumno->dni) {
                    $meta[] = 'Doc.: ' . $alumno->dni;
                }

                if ($alumno->telefono) {
                    $meta[] = $alumno->telefono;
                }

                if (!$alumno->activo) {
                    $meta[] = 'Baja';
                }

                return [
                    'type' => 'alumno',
                    'title' => trim($alumno->nombre . ' ' . $alumno->apellidos),
                    'subtitle' => $alumno->email ?: 'Alumno del club',
                    'url' => route('panel.alumnos.show', $alumno),
                    'meta' => implode(' · ', $meta),
                    'image_url' => $alumno->foto_url,
                ];
            });
    }

    protected function buscarGrupos(array $terminos, int $limite): Collection
    {
        return Grupo::query()
            ->select(['id', 'nombre', 'color', 'activo'])
            ->withCount(['alumnosActivos as alumnos_activos_count'])
            ->when($terminos !== [], fn (Builder $query) => $this->aplicarBusquedaPorTerminos(
                $query,
                $terminos,
                ['nombre']
            ))
            ->orderByDesc('activo')
            ->orderBy('nombre')
            ->limit($limite)
            ->get()
            ->map(function (Grupo $grupo) {
                return [
                    'type' => 'grupo',
                    'title' => $grupo->nombre,
                    'subtitle' => $grupo->activo ? 'Grupo activo' : 'Grupo inactivo',
                    'url' => route('panel.grupos.show', $grupo),
                    'meta' => ($grupo->alumnos_activos_count ?? 0) . ' alumnos activos',
                    'color' => $grupo->color_hex,
                ];
            });
    }

    protected function buscarPreinscripciones(array $terminos, int $limite): Collection
    {
        return Preinscripcion::query()
            ->select(['id', 'nombre', 'apellidos', 'email', 'telefono', 'modalidad', 'objetivo', 'estado'])
            ->when($terminos !== [], fn (Builder $query) => $this->aplicarBusquedaPorTerminos(
                $query,
                $terminos,
                ['nombre', 'apellidos', 'email', 'telefono', 'modalidad', 'objetivo']
            ))
            ->orderByRaw("FIELD(estado, 'nueva', 'en_proceso', 'resuelta', 'descartada')")
            ->orderByDesc('id')
            ->limit($limite)
            ->get()
            ->map(function (Preinscripcion $preinscripcion) {
                $meta = array_filter([
                    $preinscripcion->modalidad ? 'Modalidad: ' . $preinscripcion->modalidad : null,
                    $preinscripcion->objetivo ? 'Objetivo: ' . $preinscripcion->objetivo : null,
                ]);

                return [
                    'type' => 'preinscripcion',
                    'title' => trim($preinscripcion->nombre . ' ' . $preinscripcion->apellidos),
                    'subtitle' => 'Estado: ' . str_replace('_', ' ', $preinscripcion->estado),
                    'url' => route('panel.preinscripciones.show', $preinscripcion),
                    'meta' => implode(' · ', $meta),
                ];
            });
    }

    protected function buscarUsuarios(array $terminos, int $limite): Collection
    {
        return User::query()
            ->select(['id', 'nombre', 'apellidos', 'email', 'telefono', 'rol', 'activo'])
            ->when($terminos !== [], fn (Builder $query) => $this->aplicarBusquedaPorTerminos(
                $query,
                $terminos,
                ['nombre', 'apellidos', 'email', 'telefono']
            ))
            ->orderByDesc('activo')
            ->orderBy('nombre')
            ->limit($limite)
            ->get()
            ->map(function (User $usuario) {
                return [
                    'type' => 'usuario',
                    'title' => $usuario->nombre_completo,
                    'subtitle' => $usuario->email,
                    'url' => route('panel.usuarios.edit', $usuario),
                    'meta' => trim($usuario->rol_label . ($usuario->activo ? '' : ' · Inactivo')),
                    'image_url' => $usuario->avatar_url,
                ];
            });
    }

    protected function aplicarBusquedaPorTerminos(Builder $query, array $terminos, array $columnas): Builder
    {
        return $query->where(function (Builder $queryPrincipal) use ($terminos, $columnas) {
            foreach ($terminos as $termino) {
                $queryPrincipal->where(function (Builder $queryTermino) use ($termino, $columnas) {
                    foreach ($columnas as $columna) {
                        $queryTermino->orWhere($columna, 'like', '%' . $termino . '%');
                    }
                });
            }
        });
    }

    protected function definirSecciones(?User $usuario): array
    {
        $secciones = [
            [
                'title' => 'Dashboard',
                'subtitle' => 'Vista general del panel',
                'url' => route('panel.home'),
                'keywords' => ['inicio', 'resumen', 'home', 'dashboard', 'panel'],
            ],
            [
                'title' => 'Calendario',
                'subtitle' => 'Calendario de clases y estados',
                'url' => route('panel.calendario'),
                'keywords' => ['calendario', 'clases', 'agenda', 'mes'],
            ],
            [
                'title' => 'Mi perfil',
                'subtitle' => 'Datos personales y contraseña',
                'url' => route('profile.edit'),
                'keywords' => ['perfil', 'usuario', 'cuenta', 'contraseña'],
            ],
        ];

        if ($usuario?->puedeGestionarClub()) {
            array_push($secciones,
                [
                    'title' => 'Alumnos',
                    'subtitle' => 'Listado general de alumnos',
                    'url' => route('panel.alumnos.index'),
                    'keywords' => ['alumnos', 'listado', 'fichas'],
                ],
                [
                    'title' => 'Crear alumno',
                    'subtitle' => 'Alta manual de un nuevo alumno',
                    'url' => route('panel.alumnos.create'),
                    'keywords' => ['nuevo alumno', 'alta alumno', 'crear alumno'],
                ],
                [
                    'title' => 'Grupos',
                    'subtitle' => 'Gestión y detalle de grupos',
                    'url' => route('panel.grupos.index'),
                    'keywords' => ['grupos', 'equipos', 'listado grupos'],
                ],
                [
                    'title' => 'Crear grupo',
                    'subtitle' => 'Alta de un grupo nuevo',
                    'url' => route('panel.grupos.create'),
                    'keywords' => ['nuevo grupo', 'alta grupo', 'crear grupo'],
                ],
                [
                    'title' => 'Historial de asistencias',
                    'subtitle' => 'Listado de clases y listas pasadas',
                    'url' => route('panel.asistencias.index'),
                    'keywords' => ['asistencias', 'historial', 'listas', 'pasar lista'],
                ],
                [
                    'title' => 'Cuotas vencidas',
                    'subtitle' => 'Cuotas ya vencidas',
                    'url' => route('panel.pagos.vencidas'),
                    'keywords' => ['pagos', 'cuotas', 'vencidas', 'caducadas'],
                ],
                [
                    'title' => 'Pendientes de pago',
                    'subtitle' => 'Cuotas pendientes de cobro',
                    'url' => route('panel.pagos.pendientes'),
                    'keywords' => ['pagos', 'pendientes', 'cuotas pendientes', 'cobro'],
                ],
                [
                    'title' => 'Historial de pagos',
                    'subtitle' => 'Registro histórico de pagos',
                    'url' => route('panel.pagos.historial'),
                    'keywords' => ['pagos', 'historial', 'cobros', 'recibos'],
                ],
                [
                    'title' => 'Tipos de cuota',
                    'subtitle' => 'Configuración de tipos de cuota',
                    'url' => route('panel.pagos.tipos'),
                    'keywords' => ['tipos cuota', 'tarifas', 'cuotas'],
                ],
                [
                    'title' => 'Seguros deportivos',
                    'subtitle' => 'Gestión de seguros del alumnado',
                    'url' => route('panel.pagos.seguros.index'),
                    'keywords' => ['seguros', 'seguro deportivo', 'consell', 'federacion'],
                ],
                [
                    'title' => 'Preinscripciones',
                    'subtitle' => 'Solicitudes pendientes y convertidas',
                    'url' => route('panel.preinscripciones.index'),
                    'keywords' => ['preinscripciones', 'solicitudes', 'leads', 'contactos'],
                ],
                [
                    'title' => 'Informes',
                    'subtitle' => 'Resumen mensual y estadísticas',
                    'url' => route('panel.informes.resumen'),
                    'keywords' => ['informes', 'resumen mensual', 'estadisticas'],
                ],
            );
        }

        if ($usuario?->puedeGestionarUsuarios()) {
            array_push($secciones,
                [
                    'title' => 'Usuarios',
                    'subtitle' => 'Listado de usuarios del panel',
                    'url' => route('panel.usuarios.index'),
                    'keywords' => ['usuarios', 'entrenadores', 'administradores'],
                ],
                [
                    'title' => 'Crear usuario',
                    'subtitle' => 'Alta de usuario del panel',
                    'url' => route('panel.usuarios.create'),
                    'keywords' => ['nuevo usuario', 'alta usuario', 'crear usuario'],
                ],
            );
        }

        return $secciones;
    }

    protected function extraerTerminos(string $terminoNormalizado): array
    {
        return collect(preg_split('/\s+/', $terminoNormalizado) ?: [])
            ->map(fn ($termino) => trim($termino))
            ->filter(fn ($termino) => $termino !== '')
            ->values()
            ->all();
    }

    protected function normalizar(?string $texto): string
    {
        return Str::of((string) $texto)
            ->ascii()
            ->lower()
            ->replaceMatches('/[^a-z0-9\s]/', ' ')
            ->squish()
            ->value();
    }
}