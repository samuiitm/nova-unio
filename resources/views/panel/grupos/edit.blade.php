@extends('layouts.panel')

@section('title', 'Editar grupo | Nova Unió')

@section('content')
    <div class="flex items-start justify-between gap-4">
        <div>
            <h1 class="text-2xl font-semibold">Editar grupo</h1>
            <p class="mt-1 panel-muted">{{ $grupo->nombre }}</p>
        </div>

        <div class="flex gap-2">
            <a href="{{ route('panel.grupos.show', $grupo) }}" class="panel-icon-btn px-5 py-3">Ver</a>
            <a href="{{ route('panel.grupos.index') }}" class="panel-icon-btn px-5 py-3">Volver</a>
        </div>
    </div>

    @if($errors->any())
        <div class="mt-5 panel-card p-4">
            <div class="text-sm font-medium">Revisa estos campos:</div>
            <ul class="mt-2 text-sm panel-muted list-disc pl-5">
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="mt-5 panel-card p-6">
        <form method="POST" action="{{ route('panel.grupos.update', $grupo) }}">
            @csrf
            @method('PATCH')

            @include('panel.grupos._form', ['grupo' => $grupo])

            <div class="mt-6 flex gap-3">
                <button class="panel-btn px-6 py-3">Guardar cambios</button>
                <a href="{{ route('panel.grupos.show', $grupo) }}" class="panel-icon-btn px-6 py-3">Cancelar</a>
            </div>
        </form>
    </div>
@endsection