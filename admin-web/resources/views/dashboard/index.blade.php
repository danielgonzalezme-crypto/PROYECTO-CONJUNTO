@extends('layouts.app')

@section('titulo', 'Dashboard')

@section('contenido')
    <h1>Resumen por torre</h1>

    @if ($error)
        <div class="aviso-error">{{ $error }} Se muestra solo la información de personal.</div>
    @endif

    <div class="resumen">
        <div class="dato"><span>{{ collect($torres)->sum('residentes') }}</span> Residentes</div>
        <div class="dato"><span>{{ collect($torres)->sum('morosos') }}</span> En mora</div>
        <div class="dato">
            <span>${{ number_format(collect($torres)->sum('deuda_total'), 0, ',', '.') }}</span> Deuda total
        </div>
    </div>

    <div class="torres">
        @forelse ($torres as $torre)
            <section class="torre">
                <h2>{{ $torre['nombre'] === 'SIN TORRE' ? 'Sin torre' : 'Torre ' . $torre['nombre'] }}</h2>
                <p>Residentes: <strong>{{ $torre['residentes'] }}</strong>
                   · En mora: <strong>{{ $torre['morosos'] }}</strong></p>
                <p>Deuda total: <strong>${{ number_format($torre['deuda_total'], 0, ',', '.') }}</strong></p>
                <p>Con parqueadero: <strong>{{ $torre['con_parqueadero'] }}</strong></p>

                <h3>Personal asignado</h3>
                @forelse ($torre['personal'] as $cargo => $nombres)
                    <p><strong>{{ $cargo }}:</strong> {{ implode(', ', $nombres) }}</p>
                @empty
                    <p class="alerta">Sin personal asignado</p>
                @endforelse
            </section>
        @empty
            <p>No hay información para mostrar.</p>
        @endforelse
    </div>
@endsection