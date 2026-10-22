@extends('layouts.app')

@section('titulo', 'Residentes')

@section('contenido')
    <h1>Residentes</h1>

    @if ($error)
        <div class="aviso-error">{{ $error }} Verifique que el servicio esté encendido.</div>
    @endif

    @if ($aviso)
        <div class="aviso-error">{{ $aviso }} Verifique que el servicio esté encendido.</div>
    @endif

    <form method="GET" action="{{ route('residentes.index') }}" class="filtro">
        <input type="text" name="buscar" placeholder="Buscar por nombre, cédula, teléfono o correo" value="{{ $buscar }}">
        <input type="text" name="torre" placeholder="Filtrar por torre" value="{{ $torre }}">
        <button class="btn" type="submit">Buscar</button>
    </form>
<div class="tabla-scroll">
    <table>
        <thead>
            <tr>
                <th>Cédula</th><th>Nombre</th><th>Torre</th><th>Apto</th>
                <th>Deuda actual</th><th>Parqueadero</th><th>Teléfono</th><th>Correo</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($lista as $residente)
                <tr>
                    <td>{{ $residente['cedula'] }}</td>
                    <td>{{ $residente['nombre'] }}</td>
                    <td>{{ $residente['torre'] }}</td>
                    <td>{{ $residente['apartamento'] }}</td>
                    <td>${{ number_format($residente['deudaActual'], 0, ',', '.') }}</td>
                    <td>
                        @if ($residente['tieneParqueadero'])
                            <a href="{{ route('parqueaderos.index', ['cedula' => $residente['cedula']]) }}" style="text-decoration: none;" title="Inspeccionar parqueadero">
                                <span style="background: #ecfdf5; color: #047857; font-weight: 700; padding: 3px 8px; border-radius: 6px; font-size: 0.85rem; border: 1px solid #a7f3d0; display: inline-flex; align-items: center; gap: 4px;">
                                    Sí
                                    @if (!empty($residente['contacto']['numeroParqueadero']))
                                        · <strong>{{ $residente['contacto']['numeroParqueadero'] }}</strong>
                                    @endif
                                </span>
                            </a>
                        @else
                            <span style="color: #94a3b8; font-size: 0.88rem;">No</span>
                        @endif
                    </td>
                    <td>{{ $residente['contacto']['telefono'] ?? '—' }}</td>
                    <td>{{ $residente['contacto']['correo'] ?? '—' }}</td>
                </tr>
            @empty
                <tr><td colspan="8">No hay residentes para mostrar.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
