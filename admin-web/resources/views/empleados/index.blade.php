@extends('layouts.app')

@section('titulo', 'Gestión de Empleados')

@section('estilos')
<style>
    /* Page Header */
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1.25rem;
    }

    .breadcrumbs {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.82rem;
        color: #64748b;
        margin-bottom: 0.5rem;
    }

    .breadcrumbs a {
        color: #64748b;
        text-decoration: none;
    }

    .breadcrumbs a:hover {
        color: #2563eb;
    }

    .breadcrumbs svg {
        width: 14px;
        height: 14px;
        stroke-width: 2;
    }

    .page-title {
        font-size: 1.85rem;
        font-weight: 800;
        color: #0f172a;
        margin: 0;
        letter-spacing: -0.025em;
        line-height: 1.2;
    }

    .page-subtitle {
        color: #64748b;
        margin: 0.4rem 0 0;
        font-size: 0.95rem;
    }

    /* KPI Cards */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .kpi-card {
        background: #fff;
        padding: 1.35rem 1.5rem;
        border-radius: 14px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.05);
        display: flex;
        align-items: center;
        gap: 1.25rem;
        transition: transform 0.15s ease, box-shadow 0.15s ease;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px -4px rgba(0, 0, 0, 0.08);
    }

    .kpi-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .kpi-icon svg {
        width: 26px;
        height: 26px;
    }

    .kpi-icon-blue { background: #eff6ff; color: #2563eb; }
    .kpi-icon-emerald { background: #ecfdf5; color: #059669; }
    .kpi-icon-amber { background: #fffbeb; color: #d97706; }
    .kpi-icon-purple { background: #f5f3ff; color: #7c3aed; }

    .kpi-content {
        flex: 1;
    }

    .kpi-label {
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        margin-bottom: 0.25rem;
    }

    .kpi-value-row {
        display: flex;
        align-items: baseline;
        gap: 0.6rem;
    }

    .kpi-value {
        font-size: 1.85rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
    }

    .kpi-pill {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 0.15rem 0.55rem;
        border-radius: 9999px;
    }

    .kpi-pill-success {
        background: #dcfce7;
        color: #15803d;
    }

    .kpi-pill-muted {
        background: #f1f5f9;
        color: #475569;
    }

    /* Filters & Controls Bar */
    .filter-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        padding: 1.25rem 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    }

    .filter-grid {
        display: flex;
        align-items: center;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .search-input-wrapper {
        position: relative;
        flex: 2;
        min-width: 250px;
    }

    .search-input-wrapper svg {
        position: absolute;
        left: 0.95rem;
        top: 50%;
        transform: translateY(-50%);
        width: 18px;
        height: 18px;
        color: #94a3b8;
        pointer-events: none;
    }

    .search-input-wrapper input {
        padding-left: 2.6rem;
        border-radius: 9px;
        font-size: 0.88rem;
    }

    .filter-dropdown-wrapper {
        flex: 1;
        min-width: 155px;
    }

    .filter-dropdown-wrapper select {
        border-radius: 9px;
        font-size: 0.88rem;
        cursor: pointer;
    }

    .btn-reset-filters {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
        border-radius: 8px;
        padding: 0.65rem 0.95rem;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        transition: all 0.15s ease;
    }

    .btn-reset-filters:hover {
        background: #e2e8f0;
        color: #0f172a;
    }

    .filter-stats-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1rem;
        padding-top: 0.9rem;
        border-top: 1px solid #f1f5f9;
        font-size: 0.84rem;
        color: #64748b;
    }

    /* Table Container */
    .table-container {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        overflow: hidden;
    }

    .table-header-custom th {
        background: #f8fafc;
        color: #475569;
        font-size: 0.74rem;
        font-weight: 700;
        letter-spacing: 0.06em;
        text-transform: uppercase;
        padding: 1rem 1.25rem;
        border-bottom: 1.5px solid #e2e8f0;
    }

    .employee-row td {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
    }

    .employee-row:hover td {
        background-color: #f8fafc;
    }

    /* Employee Profile Column */
    .employee-info-cell {
        display: flex;
        align-items: center;
        gap: 0.9rem;
    }

    .employee-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 0.88rem;
        flex-shrink: 0;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
    }

    .employee-details {
        display: flex;
        flex-direction: column;
    }

    .employee-name {
        font-weight: 700;
        color: #0f172a;
        font-size: 0.94rem;
        line-height: 1.3;
    }

    .employee-cedula {
        font-size: 0.78rem;
        color: #64748b;
        font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
        letter-spacing: 0.02em;
    }

    /* Role Badges */
    .role-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.35rem 0.75rem;
        border-radius: 8px;
        font-size: 0.82rem;
        font-weight: 600;
        line-height: 1;
    }

    .role-badge svg {
        width: 14px;
        height: 14px;
    }

    .role-vigilante {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .role-aseador {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
    }

    .role-mantenimiento {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .role-administrativo {
        background: #f5f3ff;
        color: #6d28d9;
        border: 1px solid #ddd6fe;
    }

    /* Turno Badges */
    .shift-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        font-size: 0.82rem;
        font-weight: 600;
        padding: 0.35rem 0.75rem;
        border-radius: 7px;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        color: #334155;
    }

    .shift-icon {
        width: 15px;
        height: 15px;
        flex-shrink: 0;
    }

    .shift-icon-sun {
        color: #d97706;
    }

    .shift-icon-sunset {
        color: #ea580c;
    }

    .shift-icon-moon {
        color: #4f46e5;
    }

    /* Tower Badge */
    .tower-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        padding: 0.3rem 0.7rem;
        border-radius: 6px;
        font-weight: 700;
        font-size: 0.82rem;
        color: #1e293b;
    }

    .tower-pill svg {
        width: 14px;
        height: 14px;
        color: #64748b;
    }

    /* Status Pills */
    .status-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        padding: 0.3rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .status-dot {
        width: 7px;
        height: 7px;
        border-radius: 9999px;
    }

    .status-active {
        background: #ecfdf5;
        color: #065f46;
        border: 1px solid #a7f3d0;
    }

    .status-active .status-dot {
        background: #10b981;
        box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.25);
    }

    .status-inactive {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #cbd5e1;
    }

    .status-inactive .status-dot {
        background: #94a3b8;
    }

    /* Action Buttons */
    .actions-group {
        display: flex;
        align-items: center;
        gap: 0.45rem;
        justify-content: flex-end;
    }

    .btn-action {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.42rem 0.75rem;
        border-radius: 7px;
        font-size: 0.8rem;
        font-weight: 600;
        text-decoration: none;
        cursor: pointer;
        font-family: inherit;
        transition: all 0.15s ease;
    }

    .btn-action-edit {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .btn-action-edit:hover {
        background: #dbeafe;
        color: #1e40af;
        border-color: #93c5fd;
    }

    .btn-action-delete {
        background: #fff;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    .btn-action-delete:hover {
        background: #fef2f2;
        color: #b91c1c;
        border-color: #fca5a5;
    }

    .btn-action svg {
        width: 14px;
        height: 14px;
        stroke-width: 2;
    }

    /* Empty State */
    .empty-state-box {
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-state-icon {
        width: 64px;
        height: 64px;
        margin: 0 auto 1.25rem;
        background: #f1f5f9;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #94a3b8;
    }

    .empty-state-icon svg {
        width: 32px;
        height: 32px;
    }

    .empty-state-title {
        font-size: 1.15rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 0.4rem;
    }

    .empty-state-desc {
        color: #64748b;
        font-size: 0.9rem;
        max-width: 400px;
        margin: 0 auto 1.5rem;
    }

    @media (max-width: 900px) {
        .filter-grid { flex-direction: column; align-items: stretch; }
        .search-input-wrapper { min-width: 100%; }
        .filter-dropdown-wrapper { min-width: 100%; }
        .actions-group { justify-content: flex-start; }
    }
</style>
@endsection

@section('contenido')

@php
    $total = $empleados->count();
    $activos = $empleados->where('activo', true)->count();
    $inactivos = $total - $activos;
    $porcentajeActivos = $total > 0 ? round(($activos / $total) * 100) : 0;
    $vigilantes = $empleados->where('cargo', 'Vigilante')->count();
    $aseadores = $empleados->where('cargo', 'Aseador')->count();
    $mantenimiento = $empleados->where('cargo', 'Mantenimiento')->count();
    $administrativos = $empleados->where('cargo', 'Administrativo')->count();

    // Paleta de avatares según inicial
    $avatarColors = [
        ['bg' => '#e0e7ff', 'text' => '#3730a3'],
        ['bg' => '#ecfdf5', 'text' => '#065f46'],
        ['bg' => '#fef3c7', 'text' => '#92400e'],
        ['bg' => '#fce7f3', 'text' => '#9d174d'],
        ['bg' => '#e0f2fe', 'text' => '#075985'],
        ['bg' => '#ede9fe', 'text' => '#5b21b6'],
    ];
@endphp

<!-- Page Header & Action -->
<div class="page-header">
    <div>
        <div class="breadcrumbs">
            <a href="{{ route('dashboard.index') }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/>
                </svg>
            </a>
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <polyline points="9 18 15 12 9 6"></polyline>
            </svg>
            <span>Personal & Empleados</span>
        </div>
        <h1 class="page-title">Directorio de Empleados</h1>
        <p class="page-subtitle">Supervisión, administración de turnos y asignación de torres en el conjunto residencial.</p>
    </div>

    <div>
        <a class="btn btn-grande" href="{{ route('empleados.create') }}">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="12" y1="5" x2="12" y2="19"></line>
                <line x1="5" y1="12" x2="19" y2="12"></line>
            </svg>
            Nuevo empleado
        </a>
    </div>
</div>

<!-- KPI Summary Cards -->
<div class="kpi-grid">
    <!-- Total Empleados -->
    <div class="kpi-card">
        <div class="kpi-icon kpi-icon-blue">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                <circle cx="9" cy="7" r="4"/>
                <path d="M23 21v-2a4 4 0 0 0-3-3.87"/>
                <path d="M16 3.13a4 4 0 0 1 0 7.75"/>
            </svg>
        </div>
        <div class="kpi-content">
            <div class="kpi-label">Total Empleados</div>
            <div class="kpi-value-row">
                <span class="kpi-value" id="kpi-total">{{ $total }}</span>
                <span class="kpi-pill kpi-pill-muted">Registrados</span>
            </div>
        </div>
    </div>

    <!-- Personal Activo -->
    <div class="kpi-card">
        <div class="kpi-icon kpi-icon-emerald">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
                <polyline points="22 4 12 14.01 9 11.01"></polyline>
            </svg>
        </div>
        <div class="kpi-content">
            <div class="kpi-label">En Servicio (Activos)</div>
            <div class="kpi-value-row">
                <span class="kpi-value">{{ $activos }}</span>
                <span class="kpi-pill kpi-pill-success">{{ $porcentajeActivos }}% Operativo</span>
            </div>
        </div>
    </div>

    <!-- Personal Inactivo -->
    <div class="kpi-card">
        <div class="kpi-icon kpi-icon-amber">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="10" y1="15" x2="10" y2="9"></line>
                <line x1="14" y1="15" x2="14" y2="9"></line>
            </svg>
        </div>
        <div class="kpi-content">
            <div class="kpi-label">Inactivos / Pausa</div>
            <div class="kpi-value-row">
                <span class="kpi-value">{{ $inactivos }}</span>
                <span class="kpi-pill kpi-pill-muted">Sin asignar</span>
            </div>
        </div>
    </div>

    <!-- Distribución de Roles -->
    <div class="kpi-card">
        <div class="kpi-icon kpi-icon-purple">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <rect x="2" y="7" width="20" height="14" rx="2" ry="2"></rect>
                <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"></path>
            </svg>
        </div>
        <div class="kpi-content">
            <div class="kpi-label">Distribución Principal</div>
            <div class="kpi-value-row">
                <span class="kpi-value" style="font-size: 1.15rem; font-weight: 700; color: #475569;">
                    {{ $vigilantes }} Vig · {{ $aseadores }} Aseo
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Search & Instant Filters Toolbar -->
<div class="filter-card">
    <div class="filter-grid">
        <!-- Search bar -->
        <div class="search-input-wrapper">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <input type="text" id="buscador" placeholder="Buscar por nombre, cédula o torre..." autocomplete="off">
        </div>

        <!-- Filter by Cargo -->
        <div class="filter-dropdown-wrapper">
            <select id="filtro-cargo">
                <option value="">Todos los cargos</option>
                @foreach (\App\Models\Empleado::CARGOS as $cargo)
                    <option value="{{ $cargo }}">{{ $cargo }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter by Turno -->
        <div class="filter-dropdown-wrapper">
            <select id="filtro-turno">
                <option value="">Todos los turnos</option>
                @foreach (\App\Models\Empleado::TURNOS as $turno)
                    <option value="{{ $turno }}">{{ $turno }}</option>
                @endforeach
            </select>
        </div>

        <!-- Filter by Estado -->
        <div class="filter-dropdown-wrapper">
            <select id="filtro-estado">
                <option value="">Todos los estados</option>
                <option value="Activo">Activos</option>
                <option value="Inactivo">Inactivos</option>
            </select>
        </div>

        <!-- Reset Button -->
        <button type="button" id="btn-reset" class="btn-reset-filters" style="display: none;">
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <line x1="18" y1="6" x2="6" y2="18"></line>
                <line x1="6" y1="6" x2="18" y2="18"></line>
            </svg>
            Limpiar
        </button>
    </div>

    <div class="filter-stats-bar">
        <span id="contador-empleados">Mostrando <strong>{{ $total }}</strong> empleado{{ $total !== 1 ? 's' : '' }}</span>
        <span style="font-size: 0.8rem; color: #94a3b8;">Presiona <kbd style="background: #f1f5f9; padding: 2px 5px; border-radius: 4px; border: 1px solid #cbd5e1;">/</kbd> para buscar</span>
    </div>
</div>

<!-- Employees Table Container -->
<div class="table-container">
    <div class="tabla-scroll">
        <table id="tabla-empleados">
            <thead>
                <tr class="table-header-custom">
                    <th>Empleado</th>
                    <th>Cargo</th>
                    <th>Turno</th>
                    <th>Torre Asignada</th>
                    <th>Estado</th>
                    <th style="text-align: right;">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($empleados as $index => $empleado)
                    @php
                        $colorIndex = crc32($empleado->cedula) % count($avatarColors);
                        $avatar = $avatarColors[abs($colorIndex)];
                        $initials = collect(explode(' ', $empleado->nombre))
                            ->filter()
                            ->take(2)
                            ->map(fn($w) => strtoupper(substr($w, 0, 1)))
                            ->implode('');
                    @endphp
                    <tr class="employee-row"
                        data-nombre="{{ strtolower($empleado->nombre) }}"
                        data-cedula="{{ $empleado->cedula }}"
                        data-cargo="{{ $empleado->cargo }}"
                        data-turno="{{ $empleado->turno }}"
                        data-torre="{{ strtolower($empleado->torre_asignada) }}"
                        data-estado="{{ $empleado->activo ? 'Activo' : 'Inactivo' }}">

                        <!-- Empleado Info (Avatar + Nombre + Cédula) -->
                        <td>
                            <div class="employee-info-cell">
                                <div class="employee-avatar" style="background-color: {{ $avatar['bg'] }}; color: {{ $avatar['text'] }};">
                                    {{ $initials ?: 'EM' }}
                                </div>
                                <div class="employee-details">
                                    <span class="employee-name">{{ $empleado->nombre }}</span>
                                    <span class="employee-cedula">Cédula: {{ $empleado->cedula }}</span>
                                </div>
                            </div>
                        </td>

                        <!-- Cargo Badge -->
                        <td>
                            @switch($empleado->cargo)
                                @case('Vigilante')
                                    <span class="role-badge role-vigilante">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                        </svg>
                                        {{ $empleado->cargo }}
                                    </span>
                                    @break
                                @case('Aseador')
                                    <span class="role-badge role-aseador">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M12 2v4M12 18v4M4.93 4.93l2.83 2.83M16.24 16.24l2.83 2.83M2 12h4M18 12h4M4.93 19.07l2.83-2.83M16.24 7.76l2.83-2.83"/>
                                        </svg>
                                        {{ $empleado->cargo }}
                                    </span>
                                    @break
                                @case('Mantenimiento')
                                    <span class="role-badge role-mantenimiento">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>
                                        </svg>
                                        {{ $empleado->cargo }}
                                    </span>
                                    @break
                                @case('Administrativo')
                                    <span class="role-badge role-administrativo">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                            <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                                            <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                                        </svg>
                                        {{ $empleado->cargo }}
                                    </span>
                                    @break
                                @default
                                    <span class="role-badge" style="background: #f1f5f9; color: #475569;">
                                        {{ $empleado->cargo }}
                                    </span>
                            @endswitch
                        </td>

                        <!-- Turno Pill con Iconos SVG -->
                        <td>
                            <span class="shift-pill">
                                @if($empleado->turno === 'Mañana')
                                    <!-- Sol SVG -->
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shift-icon shift-icon-sun">
                                        <circle cx="12" cy="12" r="5"></circle>
                                        <line x1="12" y1="1" x2="12" y2="3"></line>
                                        <line x1="12" y1="21" x2="12" y2="23"></line>
                                        <line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line>
                                        <line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line>
                                        <line x1="1" y1="12" x2="3" y2="12"></line>
                                        <line x1="21" y1="12" x2="23" y2="12"></line>
                                        <line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line>
                                        <line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line>
                                    </svg>
                                @elseif($empleado->turno === 'Tarde')
                                    <!-- Atardecer / Sol en el horizonte SVG -->
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shift-icon shift-icon-sunset">
                                        <path d="M17 18a5 5 0 0 0-10 0"></path>
                                        <line x1="12" y1="2" x2="12" y2="9"></line>
                                        <line x1="4.22" y1="10.22" x2="5.64" y2="11.64"></line>
                                        <line x1="1" y1="18" x2="3" y2="18"></line>
                                        <line x1="21" y1="18" x2="23" y2="18"></line>
                                        <line x1="18.36" y1="11.64" x2="19.78" y2="10.22"></line>
                                        <line x1="23" y1="22" x2="1" y2="22"></line>
                                    </svg>
                                @else
                                    <!-- Luna creciente SVG -->
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shift-icon shift-icon-moon">
                                        <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path>
                                    </svg>
                                @endif
                                {{ $empleado->turno }}
                            </span>
                        </td>

                        <!-- Torre Asignada con Icono SVG -->
                        <td>
                            <span class="tower-pill">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <path d="M3 21h18M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"/>
                                </svg>
                                Torre {{ strtoupper($empleado->torre_asignada) }}
                            </span>
                        </td>

                        <!-- Estado -->
                        <td>
                            @if ($empleado->activo)
                                <span class="status-pill status-active">
                                    <span class="status-dot"></span>
                                    Activo
                                </span>
                            @else
                                <span class="status-pill status-inactive">
                                    <span class="status-dot"></span>
                                    Inactivo
                                </span>
                            @endif
                        </td>

                        <!-- Acciones -->
                        <td>
                            <div class="actions-group">
                                <a class="btn-action btn-action-edit" href="{{ route('empleados.edit', $empleado) }}" title="Editar información de este empleado">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                        <path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
                                        <path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
                                    </svg>
                                    Editar
                                </a>

                                <form action="{{ route('empleados.destroy', $empleado) }}" method="POST"
                                      style="display:inline" onsubmit="return confirm('¿Confirmas que deseas eliminar a {{ addslashes($empleado->nombre) }} de la lista de personal?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn-action btn-action-delete" type="submit" title="Eliminar empleado">
                                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor">
                                            <polyline points="3 6 5 6 21 6"></polyline>
                                            <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
                                        </svg>
                                        Eliminar
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr id="empty-db-row">
                        <td colspan="6">
                            <div class="empty-state-box">
                                <div class="empty-state-icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                                        <circle cx="9" cy="7" r="4"/>
                                    </svg>
                                </div>
                                <h3 class="empty-state-title">No hay empleados registrados</h3>
                                <p class="empty-state-desc">Comienza registrando al primer colaborador para asignarlo a torres y turnos de vigilancia o mantenimiento.</p>
                                <a class="btn" href="{{ route('empleados.create') }}">
                                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="12" y1="5" x2="12" y2="19"></line>
                                        <line x1="5" y1="12" x2="19" y2="12"></line>
                                    </svg>
                                    Registrar empleado
                                </a>
                            </div>
                        </td>
                    </tr>
                @endforelse

                <!-- Fila dinámica cuando la búsqueda de Javascript no da resultados -->
                <tr id="no-filter-match-row" style="display: none;">
                    <td colspan="6">
                        <div class="empty-state-box">
                            <div class="empty-state-icon">
                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <circle cx="11" cy="11" r="8"></circle>
                                    <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                </svg>
                            </div>
                            <h3 class="empty-state-title">No se encontraron resultados</h3>
                            <p class="empty-state-desc">No hay empleados que coincidan con los filtros aplicados. Intenta probar otros criterios.</p>
                            <button type="button" class="btn btn-outline" onclick="resetFilters()">
                                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                                Limpiar filtros de búsqueda
                            </button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const buscador = document.getElementById('buscador');
        const filtroCargo = document.getElementById('filtro-cargo');
        const filtroTurno = document.getElementById('filtro-turno');
        const filtroEstado = document.getElementById('filtro-estado');
        const btnReset = document.getElementById('btn-reset');
        const contador = document.getElementById('contador-empleados');
        const rows = document.querySelectorAll('.employee-row');
        const noMatchRow = document.getElementById('no-filter-match-row');
        const totalRows = rows.length;

        function filtrar() {
            const query = (buscador.value || '').trim().toLowerCase();
            const cargo = filtroCargo.value;
            const turno = filtroTurno.value;
            const estado = filtroEstado.value;

            const isFiltered = query !== '' || cargo !== '' || turno !== '' || estado !== '';
            btnReset.style.display = isFiltered ? 'inline-flex' : 'none';

            let visibleCount = 0;

            rows.forEach(row => {
                const rowNombre = row.getAttribute('data-nombre') || '';
                const rowCedula = row.getAttribute('data-cedula') || '';
                const rowCargo = row.getAttribute('data-cargo') || '';
                const rowTurno = row.getAttribute('data-turno') || '';
                const rowTorre = row.getAttribute('data-torre') || '';
                const rowEstado = row.getAttribute('data-estado') || '';

                const matchesQuery = query === '' ||
                    rowNombre.includes(query) ||
                    rowCedula.includes(query) ||
                    rowTorre.includes(query);

                const matchesCargo = cargo === '' || rowCargo === cargo;
                const matchesTurno = turno === '' || rowTurno === turno;
                const matchesEstado = estado === '' || rowEstado === estado;

                if (matchesQuery && matchesCargo && matchesTurno && matchesEstado) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (noMatchRow) {
                noMatchRow.style.display = (visibleCount === 0 && totalRows > 0) ? '' : 'none';
            }

            if (contador) {
                if (isFiltered) {
                    contador.innerHTML = `Mostrando <strong>${visibleCount}</strong> de <strong>${totalRows}</strong> empleados coincidentes`;
                } else {
                    contador.innerHTML = `Mostrando <strong>${totalRows}</strong> empleado${totalRows !== 1 ? 's' : ''}`;
                }
            }
        }

        window.resetFilters = function() {
            buscador.value = '';
            filtroCargo.value = '';
            filtroTurno.value = '';
            filtroEstado.value = '';
            filtrar();
            buscador.focus();
        };

        buscador.addEventListener('input', filtrar);
        filtroCargo.addEventListener('change', filtrar);
        filtroTurno.addEventListener('change', filtrar);
        filtroEstado.addEventListener('change', filtrar);
        btnReset.addEventListener('click', window.resetFilters);

        // Atajo de teclado '/' para enfocar buscador
        document.addEventListener('keydown', function(e) {
            if (e.key === '/' && document.activeElement !== buscador) {
                e.preventDefault();
                buscador.focus();
            }
        });
    });
</script>
@endsection