@extends('layouts.panel')

@section('title', 'Mi perfil | Nova Unió')

@section('content')
<div class="flex items-start justify-between gap-4 flex-wrap">
    <div>
        <h1 class="text-2xl font-semibold">Mi perfil</h1>
        <p class="mt-1 panel-muted">Actualiza tus datos personales y tu contraseña.</p>
    </div>

    <a href="{{ route('panel.home') }}" class="panel-icon-btn px-5 py-3">Volver</a>
</div>

@if(session('status') === 'profile-updated')
    <div class="mt-5 panel-card p-4">
        <div class="text-sm">Perfil actualizado correctamente.</div>
    </div>
@endif

@if(session('status') === 'password-updated')
    <div class="mt-5 panel-card p-4">
        <div class="text-sm">Contraseña actualizada correctamente.</div>
    </div>
@endif

@if(session('error'))
    <div class="mt-5 panel-card p-4">
        <div class="text-sm">{{ session('error') }}</div>
    </div>
@endif

<div class="mt-5 grid gap-5 xl:grid-cols-[1.2fr,.8fr]">
    @include('profile.partials.update-profile-information-form')
    @include('profile.partials.update-password-form')
</div>
@endsection