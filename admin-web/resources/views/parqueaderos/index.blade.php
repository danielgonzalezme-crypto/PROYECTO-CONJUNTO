@extends('layouts.app')

@section('titulo', 'Control de Parqueaderos')

@section('estilos')
<style>
    .parq-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 2rem;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .parq-header-text h1 {
        margin: 0 0 0.4rem 0;
        font-size: 1.85rem;
        font-weight: 800;
        color: var(--color-slate-900);
        letter-spacing: -0.03em;
    }

    .parq-header-text p {
        margin: 0;
        color: var(--color-slate-500);
        font-size: 0.95rem;
    }

    .metrics-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        gap: 1.25rem;
        margin-bottom: 2rem;
    }

    .metric-card {
        background: #ffffff;
        border: 1px solid var(--color-slate-200);
        border-radius: var(--radius-lg);
        padding: 1.25rem 1.5rem;
        box-shadow: var(--shadow-sm);
        display: flex;
        align-items: center;
        gap: 1.25rem;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }

    .metric-card:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-md);
    }

    .metric-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }

    .metric-icon.blue { background: #eff6ff; color: #2563eb; }
    .metric-icon.green { background: #ecfdf5; color: #059669; }
    .metric-icon.purple { background: #f5f3ff; color: #7c3aed; }
    .metric-icon.amber { background: #fffbeb; color: #d97706; }

    .metric-info .metric-num {
        font-size: 1.6rem;
        font-weight: 800;
        color: var(--color-slate-800);
        line-height: 1.2;
    }

    .metric-info .metric-label {
        font-size: 0.82rem;
        color: var(--color-slate-500);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.04em;
        margin-top: 0.2rem;
    }

    /* Tarjeta de Búsqueda */
    .search-panel {
        background: #ffffff;
        border: 1px solid var(--color-slate-200);
        border-radius: var(--radius-lg);
        padding: 1.75rem;
        box-shadow: var(--shadow-md);
        margin-bottom: 2rem;
    }

    .search-panel-title {
        display: flex;
        align-items: center;
        gap: 0.6rem;
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--color-slate-800);
        margin-bottom: 1rem;
    }

    .search-panel-title svg {
        width: 22px;
        height: 22px;
        color: var(--color-primary);
    }

    .search-form-row {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        margin-bottom: 1rem;
    }

    .input-cedula-wrap {
        flex: 1;
        min-width: 280px;
        position: relative;
    }

    .input-cedula-wrap input {
        width: 100%;
        padding: 0.85rem 1rem 0.85rem 2.75rem;
        font-size: 1.05rem;
        font-weight: 600;
        border: 2px solid var(--color-slate-300);
        border-radius: 10px;
        color: var(--color-slate-800);
        transition: all 0.2s ease;
    }

    .input-cedula-wrap input:focus {
        border-color: #2563eb;
        box-shadow: 0 0 0 4px rgba(37, 99, 235, 0.12);
    }

    .input-cedula-wrap .input-icon {
        position: absolute;
        left: 0.95rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--color-slate-400);
        width: 18px;
        height: 18px;
    }

    .btn-search-live {
        background: linear-gradient(135deg, #1e40af, #2563eb);
        color: #ffffff;
        border: none;
        padding: 0.85rem 1.6rem;
        font-size: 0.95rem;
        font-weight: 700;
        border-radius: 10px;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
        box-shadow: 0 4px 6px -1px rgba(37, 99, 235, 0.25);
    }

    .btn-search-live:hover {
        background: linear-gradient(135deg, #1d4ed8, #1e40af);
        transform: translateY(-1px);
        box-shadow: 0 6px 12px -2px rgba(37, 99, 235, 0.35);
    }

    .quick-demos {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
        font-size: 0.85rem;
        color: var(--color-slate-500);
    }

    .pill-demo {
        background: var(--color-slate-100);
        border: 1px solid var(--color-slate-300);
        color: var(--color-slate-700);
        padding: 0.3rem 0.75rem;
        border-radius: 9999px;
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .pill-demo:hover {
        background: var(--color-primary-light);
        border-color: var(--color-primary-border);
        color: var(--color-primary);
        transform: scale(1.03);
    }

    /* Comparador de APIs en Vivo */
    .live-result-container {
        display: none;
        margin-top: 1.5rem;
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(6px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .apis-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 1.5rem;
        margin-top: 1rem;
    }

    .api-card {
        border-radius: var(--radius-lg);
        padding: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .api-card.java {
        background: #f8fafc;
        border: 2px solid #bfdbfe;
    }

    .api-card.node {
        background: #f8fafc;
        border: 2px solid #a7f3d0;
    }

    .api-badge-tag {
        display: inline-flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        padding: 0.25rem 0.65rem;
        border-radius: 9999px;
        margin-bottom: 1rem;
    }

    .api-badge-tag.java {
        background: #dbeafe;
        color: #1e40af;
    }

    .api-badge-tag.node {
        background: #d1fae5;
        color: #065f46;
    }

    .api-field {
        margin-bottom: 0.85rem;
    }

    .api-field:last-child {
        margin-bottom: 0;
    }

    .api-field-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--color-slate-500);
        text-transform: uppercase;
        letter-spacing: 0.03em;
        margin-bottom: 0.15rem;
    }

    .api-field-value {
        font-size: 1.05rem;
        font-weight: 700;
        color: var(--color-slate-800);
    }

    /* Placa vehicular estilo real */
    .plate-badge {
        display: inline-block;
        background: #fbbf24;
        border: 2px solid #000000;
        color: #000000;
        font-family: monospace, sans-serif;
        font-size: 1.15rem;
        font-weight: 900;
        letter-spacing: 0.15em;
        padding: 0.25rem 0.8rem;
        border-radius: 6px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        text-align: center;
    }

    .bay-badge {
        display: inline-block;
        background: #1e3a8a;
        color: #ffffff;
        font-size: 1.15rem;
        font-weight: 800;
        letter-spacing: 0.05em;
        padding: 0.25rem 0.85rem;
        border-radius: 8px;
    }

    .status-badge-yes {
        background: #ecfdf5;
        color: #047857;
        border: 1px solid #a7f3d0;
        padding: 0.25rem 0.65rem;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .status-badge-no {
        background: #f1f5f9;
        color: #64748b;
        border: 1px solid #cbd5e1;
        padding: 0.25rem 0.65rem;
        border-radius: 6px;
        font-size: 0.85rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    /* Tabla */
    .table-container {
        background: #ffffff;
        border: 1px solid var(--color-slate-200);
        border-radius: var(--radius-lg);
        overflow: hidden;
        box-shadow: var(--shadow-sm);
    }

    .table-header-bar {
        padding: 1.25rem 1.5rem;
        background: #ffffff;
        border-bottom: 1px solid var(--color-slate-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .table-header-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--color-slate-800);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }
</style>
@endsection

@section('contenido')
    <div class="parq-header">
        <div class="parq-header-text">
            <h1>Control de Parqueaderos & Vehículos</h1>
            <p>Consulta por cédula cruzando los servicios de <strong>Java (Spring Boot :8080)</strong> y <strong>Node.js (Express :3000)</strong>.</p>
        </div>
    </div>

    @if ($errorResidentes)
        <div class="aviso-error">
            <strong>API Java (Residentes):</strong> {{ $errorResidentes }}
        </div>
    @endif

    @if ($errorPropietarios)
        <div class="aviso-error">
            <strong>API Node.js (Propietarios):</strong> {{ $errorPropietarios }}
        </div>
    @endif

    <!-- Tarjetas de Métricas Globales -->
    <div class="metrics-grid">
        <div class="metric-card">
            <div class="metric-icon blue">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="4"/>
                    <path d="M9 17V7h4a3 3 0 0 1 0 6H9"/>
                </svg>
            </div>
            <div class="metric-info">
                <div class="metric-num">{{ $estadisticas['cuposAsignados'] }}</div>
                <div class="metric-label">Cupos Asignados (Java)</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon purple">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.5 2.6C1.4 11 1 11.9 1 13v3c0 .6.4 1 1 1h2"/>
                    <circle cx="7" cy="17" r="2"/>
                    <path d="M9 17h6"/>
                    <circle cx="17" cy="17" r="2"/>
                </svg>
            </div>
            <div class="metric-info">
                <div class="metric-num">{{ $estadisticas['bahiasRegistradas'] }}</div>
                <div class="metric-label">Bahías Registradas (Node)</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon green">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="5" width="18" height="14" rx="2"/>
                    <path d="M7 15h4M15 15h2M7 9h10"/>
                </svg>
            </div>
            <div class="metric-info">
                <div class="metric-num">{{ $estadisticas['vehiculosRegistrados'] }}</div>
                <div class="metric-label">Placas Autorizadas (Node)</div>
            </div>
        </div>

        <div class="metric-card">
            <div class="metric-icon amber">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/>
                    <circle cx="9" cy="7" r="4"/>
                </svg>
            </div>
            <div class="metric-info">
                <div class="metric-num">{{ $estadisticas['totalRegistros'] }}</div>
                <div class="metric-label">Total en Registro</div>
            </div>
        </div>
    </div>

    <!-- Panel de Consulta por Cédula -->
    <div class="search-panel">
        <div class="search-panel-title">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="11" cy="11" r="8"></circle>
                <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
            </svg>
            <span>Consultar Información de Parqueadero por Cédula</span>
        </div>

        <div class="search-form-row">
            <div class="input-cedula-wrap">
                <svg class="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                    <circle cx="12" cy="7" r="4"></circle>
                </svg>
                <input 
                    type="text" 
                    id="cedulaInput" 
                    placeholder="Escriba el número de cédula (ej. 1000000001)..." 
                    value="{{ $cedulaBuscada }}"
                    autocomplete="off"
                >
            </div>
            <button type="button" id="btnConsultarLive" class="btn-search-live">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <polyline points="22 12 18 12 15 21 9 3 6 12 2 12"></polyline>
                </svg>
                Consultar APIs
            </button>
        </div>

        <!-- Cédulas Demo Rápidas para pruebas de 1-clic -->
        <div class="quick-demos">
            <span>Probar con datos demo:</span>
            <button type="button" class="pill-demo" onclick="probarCedula('1000000001')">1000000001 (Ana · P-01)</button>
            <button type="button" class="pill-demo" onclick="probarCedula('1000000002')">1000000002 (Luis · Sin cupo)</button>
            <button type="button" class="pill-demo" onclick="probarCedula('1000000003')">1000000003 (Marta · P-07)</button>
            <button type="button" class="pill-demo" onclick="probarCedula('1000000004')">1000000004 (Pedro · Sin cupo)</button>
        </div>

        <!-- Mensaje de Carga / Error -->
        <div id="statusMensaje" style="display: none; margin-top: 1rem; font-weight: 600;"></div>

        <!-- Resultados en Vivo (Cajas Comparativas de Java y Node) -->
        <div id="liveResultContainer" class="live-result-container">
            <div class="apis-grid">
                
                <!-- Tarjeta Java -->
                <div class="api-card java">
                    <div class="api-badge-tag java">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="16 18 22 12 16 6"></polyline>
                            <polyline points="8 6 2 12 8 18"></polyline>
                        </svg>
                        API Residentes (Java :8080)
                    </div>

                    <div class="api-field">
                        <div class="api-field-label">Nombre del Residente</div>
                        <div class="api-field-value" id="cardJavaNombre">—</div>
                    </div>

                    <div class="api-field">
                        <div class="api-field-label">Ubicación</div>
                        <div class="api-field-value" id="cardJavaUbicacion">—</div>
                    </div>

                    <div class="api-field">
                        <div class="api-field-label">Estado Administrativo Parqueadero</div>
                        <div class="api-field-value" id="cardJavaTieneParqueadero">—</div>
                    </div>

                    <div class="api-field">
                        <div class="api-field-label">Deuda Actual</div>
                        <div class="api-field-value" id="cardJavaDeuda">—</div>
                    </div>

                    <div class="api-field" style="margin-top: 1rem; border-top: 1px dashed #bfdbfe; padding-top: 0.75rem;">
                        <small style="color: #64748b; font-size: 0.75rem;">Endpoint consultado: <code>GET /api/residentes/{cedula}</code></small>
                    </div>
                </div>

                <!-- Tarjeta Node.js -->
                <div class="api-card node">
                    <div class="api-badge-tag node">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12 2L2 7l10 5 10-5-10-5zM2 17l10 5 10-5M2 12l10 5 10-5"></path>
                        </svg>
                        API Propietarios (Node.js :3000)
                    </div>

                    <div class="api-field">
                        <div class="api-field-label">Número de Bahía / Parqueadero</div>
                        <div class="api-field-value" id="cardNodeBahia">—</div>
                    </div>

                    <div class="api-field">
                        <div class="api-field-label">Placa del Vehículo Autorizado</div>
                        <div class="api-field-value" id="cardNodePlaca">—</div>
                    </div>

                    <div class="api-field">
                        <div class="api-field-label">Tipo de Residente</div>
                        <div class="api-field-value" id="cardNodeTipo">—</div>
                    </div>

                    <div class="api-field">
                        <div class="api-field-label">Teléfono & Correo</div>
                        <div class="api-field-value" id="cardNodeContacto" style="font-size: 0.95rem;">—</div>
                    </div>

                    <div class="api-field" style="margin-top: 1rem; border-top: 1px dashed #a7f3d0; padding-top: 0.75rem;">
                        <small style="color: #64748b; font-size: 0.75rem;">Endpoint consultado: <code>GET /api/propietarios/{cedula}</code></small>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Tabla General de Parqueaderos -->
    <div class="table-container">
        <div class="table-header-bar">
            <div class="table-header-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="3" width="18" height="18" rx="4"/>
                    <path d="M9 17V7h4a3 3 0 0 1 0 6H9"/>
                </svg>
                <span>Registro Consolidado de Parqueaderos</span>
            </div>
            <div style="font-size: 0.88rem; color: var(--color-slate-500);">
                Total: <strong>{{ count($parqueaderos) }}</strong> registros
            </div>
        </div>

        <div class="tabla-scroll">
            <table>
                <thead>
                    <tr>
                        <th>Cédula</th>
                        <th>Residente</th>
                        <th>Torre / Apto</th>
                        <th>Cupo (Java)</th>
                        <th>Bahía (Node)</th>
                        <th>Placa (Node)</th>
                        <th>Tipo</th>
                        <th>Teléfono</th>
                        <th>Acción</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($parqueaderos as $p)
                        <tr>
                            <td><strong>{{ $p['cedula'] }}</strong></td>
                            <td>{{ $p['nombre'] }}</td>
                            <td>Torre {{ $p['torre'] }} - {{ $p['apartamento'] }}</td>
                            <td>
                                @if ($p['tieneParqueadero'])
                                    <span class="status-badge-yes">
                                        <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3">
                                            <polyline points="20 6 9 17 4 12"></polyline>
                                        </svg>
                                        Asignado
                                    </span>
                                @else
                                    <span class="status-badge-no">Sin cupo</span>
                                @endif
                            </td>
                            <td>
                                @if ($p['numeroParqueadero'] !== 'Sin asignar')
                                    <span class="bay-badge">{{ $p['numeroParqueadero'] }}</span>
                                @else
                                    <span style="color: #94a3b8;">—</span>
                                @endif
                            </td>
                            <td>
                                @if ($p['placaVehiculo'] !== 'Sin registrar')
                                    <span class="plate-badge">{{ $p['placaVehiculo'] }}</span>
                                @else
                                    <span style="color: #94a3b8;">—</span>
                                @endif
                            </td>
                            <td>{{ $p['tipoResidente'] }}</td>
                            <td>{{ $p['telefono'] }}</td>
                            <td>
                                <button type="button" class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;" onclick="probarCedula('{{ $p['cedula'] }}')">
                                    Inspeccionar
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" style="text-align: center; padding: 2rem; color: var(--color-slate-500);">
                                No se encontraron registros de parqueadero.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Script de Consulta Interactiva -->
    <script>
        const cedulaInput = document.getElementById('cedulaInput');
        const btnConsultar = document.getElementById('btnConsultarLive');
        const statusMensaje = document.getElementById('statusMensaje');
        const liveResultContainer = document.getElementById('liveResultContainer');

        // Campos Java
        const cardJavaNombre = document.getElementById('cardJavaNombre');
        const cardJavaUbicacion = document.getElementById('cardJavaUbicacion');
        const cardJavaTieneParqueadero = document.getElementById('cardJavaTieneParqueadero');
        const cardJavaDeuda = document.getElementById('cardJavaDeuda');

        // Campos Node
        const cardNodeBahia = document.getElementById('cardNodeBahia');
        const cardNodePlaca = document.getElementById('cardNodePlaca');
        const cardNodeTipo = document.getElementById('cardNodeTipo');
        const cardNodeContacto = document.getElementById('cardNodeContacto');

        async function ejecutarConsulta() {
            const cedula = cedulaInput.value.trim();
            if (!cedula) {
                statusMensaje.style.display = 'block';
                statusMensaje.style.color = '#dc2626';
                statusMensaje.textContent = 'Por favor escribe un número de cédula para consultar.';
                liveResultContainer.style.display = 'none';
                return;
            }

            statusMensaje.style.display = 'block';
            statusMensaje.style.color = '#2563eb';
            statusMensaje.textContent = `Consultando API Java y API Node.js para cédula ${cedula}...`;
            btnConsultar.disabled = true;

            try {
                const res = await fetch(`/parqueaderos/api/${encodeURIComponent(cedula)}`);
                const data = await res.json();

                btnConsultar.disabled = false;

                if (!data.encontradoEnJava && !data.encontradoEnNode) {
                    statusMensaje.style.color = '#dc2626';
                    statusMensaje.textContent = `No se encontró ningún registro para la cédula ${cedula} en Java ni en Node.js.`;
                    liveResultContainer.style.display = 'none';
                    return;
                }

                statusMensaje.style.color = '#059669';
                statusMensaje.textContent = `¡Datos recibidos exitosamente de los microservicios para cédula ${cedula}!`;

                // 1. Renderizar Java (Spring Boot)
                if (data.encontradoEnJava && data.residente) {
                    cardJavaNombre.textContent = data.residente.nombre || '—';
                    cardJavaUbicacion.textContent = `Torre ${data.residente.torre || '—'} · Apartamento ${data.residente.apartamento || '—'}`;
                    
                    if (data.residente.tieneParqueadero) {
                        cardJavaTieneParqueadero.innerHTML = '<span class="status-badge-yes"><svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><polyline points="20 6 9 17 4 12"></polyline></svg> SÍ (Tiene cupo asignado)</span>';
                    } else {
                        cardJavaTieneParqueadero.innerHTML = '<span class="status-badge-no">NO (Sin cupo asignado)</span>';
                    }

                    const deudaFormateada = Number(data.residente.deudaActual || 0).toLocaleString('es-CO');
                    cardJavaDeuda.textContent = `$${deudaFormateada}`;
                } else {
                    cardJavaNombre.textContent = 'No registrado en Residentes';
                    cardJavaUbicacion.textContent = '—';
                    cardJavaTieneParqueadero.innerHTML = '<span class="status-badge-no">No disponible</span>';
                    cardJavaDeuda.textContent = '—';
                }

                // 2. Renderizar Node.js (Express)
                if (data.encontradoEnNode && data.propietario) {
                    if (data.propietario.numeroParqueadero) {
                        cardNodeBahia.innerHTML = `<span class="bay-badge">${data.propietario.numeroParqueadero}</span>`;
                    } else {
                        cardNodeBahia.innerHTML = '<span class="status-badge-no">Sin bahía registrada</span>';
                    }

                    if (data.propietario.placaVehiculo) {
                        cardNodePlaca.innerHTML = `<span class="plate-badge">${data.propietario.placaVehiculo}</span>`;
                    } else {
                        cardNodePlaca.innerHTML = '<span class="status-badge-no">Sin placa registrada</span>';
                    }

                    cardNodeTipo.textContent = data.propietario.tipoResidente || '—';
                    cardNodeContacto.textContent = `${data.propietario.telefono || 'Sin teléfono'} · ${data.propietario.correo || 'Sin correo'}`;
                } else {
                    cardNodeBahia.innerHTML = '<span class="status-badge-no">Sin ficha en Node.js</span>';
                    cardNodePlaca.innerHTML = '<span class="status-badge-no">Sin placa registrada</span>';
                    cardNodeTipo.textContent = '—';
                    cardNodeContacto.textContent = '—';
                }

                liveResultContainer.style.display = 'block';

            } catch (err) {
                btnConsultar.disabled = false;
                statusMensaje.style.color = '#dc2626';
                statusMensaje.textContent = 'Error al comunicar con el servidor Laravel.';
                console.error(err);
            }
        }

        btnConsultar.addEventListener('click', ejecutarConsulta);

        cedulaInput.addEventListener('keydown', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                ejecutarConsulta();
            }
        });

        function probarCedula(cedula) {
            cedulaInput.value = cedula;
            ejecutarConsulta();
            window.scrollTo({ top: document.querySelector('.search-panel').offsetTop - 20, behavior: 'smooth' });
        }

        // Si ya vino con una cédula en la URL, ejecutarla automáticamente
        @if ($cedulaBuscada)
            document.addEventListener('DOMContentLoaded', () => {
                ejecutarConsulta();
            });
        @endif
    </script>
@endsection
