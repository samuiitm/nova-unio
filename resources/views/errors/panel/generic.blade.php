@extends('layouts.panel-error')

@section('title', ($status ?? 'Error') . ' | Panel | Nova Unió')

@section('content')
@php
    $panelHome = \Illuminate\Support\Facades\Route::has('panel.home') ? route('panel.home') : url('/panel');
@endphp

<div class="w-full max-w-4xl">
    <div class="panel-card overflow-hidden">
        <div class="p-8 sm:p-10 lg:p-12">
            <div class="grid gap-8 lg:grid-cols-[auto,1fr] items-center">
                <div class="select-none leading-none font-black text-white/10 text-[6rem] sm:text-[8rem] md:text-[10rem]">
                    {{ $status ?? 'Error' }}
                </div>

                <div>
                    <p class="uppercase tracking-wider text-sm panel-muted">
                        Error del panel
                    </p>

                    <h1 class="mt-3 text-3xl sm:text-4xl font-semibold">
                        No se ha podido abrir esta página del panel
                    </h1>

                    <p class="mt-4 max-w-2xl text-sm sm:text-base panel-muted">
                        Se ha producido un error al cargar esta sección.
                        Vuelve al dashboard o inténtalo de nuevo más tarde.
                    </p>

                    <div class="mt-8 flex flex-wrap gap-3">
                        <a href="{{ $panelHome }}" class="panel-btn px-5 py-3">
                            Volver al dashboard
                        </a>
                    </div>

                    <p class="mt-6 text-xs panel-muted">
                        Código: {{ $status ?? 'error' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection