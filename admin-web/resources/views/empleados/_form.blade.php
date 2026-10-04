<style>
    .form-container-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        padding: 2.25rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -2px rgba(0, 0, 0, 0.05);
    }

    .form-section-title {
        font-size: 0.85rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-section-title::after {
        content: "";
        flex: 1;
        height: 1px;
        background: #f1f5f9;
    }

    .form-row-2 {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.25rem;
        margin-bottom: 1.25rem;
    }

    .form-group {
        margin-bottom: 1.25rem;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        font-size: 0.88rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 0.45rem;
    }

    .form-label .required-star {
        color: #ef4444;
        font-size: 0.85rem;
    }

    .form-helper {
        font-size: 0.78rem;
        color: #64748b;
        margin-top: 0.35rem;
    }

    .input-icon-wrapper {
        position: relative;
    }

    .input-icon-wrapper svg {
        position: absolute;
        left: 0.85rem;
        top: 50%;
        transform: translateY(-50%);
        width: 17px;
        height: 17px;
        color: #94a3b8;
        pointer-events: none;
    }

    .input-icon-wrapper input,
    .input-icon-wrapper select {
        padding-left: 2.45rem;
    }

    .field-error-msg {
        display: flex;
        align-items: center;
        gap: 0.35rem;
        color: #dc2626;
        font-size: 0.8rem;
        font-weight: 500;
        margin-top: 0.35rem;
    }

    .field-error-msg svg {
        width: 14px;
        height: 14px;
        flex-shrink: 0;
    }

    /* Modern Switch Card */
    .switch-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        margin-top: 1.5rem;
        cursor: pointer;
        transition: all 0.15s ease;
    }

    .switch-card:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .switch-content {
        display: flex;
        flex-direction: column;
    }

    .switch-title {
        font-size: 0.92rem;
        font-weight: 700;
        color: #1e293b;
    }

    .switch-desc {
        font-size: 0.8rem;
        color: #64748b;
    }

    /* CSS Switch */
    .switch-toggle {
        position: relative;
        display: inline-block;
        width: 46px;
        height: 26px;
        flex-shrink: 0;
    }

    .switch-toggle input {
        opacity: 0;
        width: 0;
        height: 0;
    }

    .slider {
        position: absolute;
        cursor: pointer;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-color: #cbd5e1;
        transition: .3s;
        border-radius: 9999px;
    }

    .slider:before {
        position: absolute;
        content: "";
        height: 20px;
        width: 20px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .3s;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    }

    .switch-toggle input:checked + .slider {
        background-color: #10b981;
    }

    .switch-toggle input:focus + .slider {
        box-shadow: 0 0 1px #10b981;
    }

    .switch-toggle input:checked + .slider:before {
        transform: translateX(20px);
    }

    .form-actions-bar {
        display: flex;
        align-items: center;
        justify-content: flex-end;
        gap: 1rem;
        margin-top: 2rem;
        padding-top: 1.5rem;
        border-top: 1px solid #f1f5f9;
    }

    @media (max-width: 640px) {
        .form-row-2 { grid-template-columns: 1fr; }
        .form-actions-bar { flex-direction: column-reverse; }
        .form-actions-bar .btn, .form-actions-bar a { width: 100%; text-align: center; }
    }
</style>

<div class="form-container-card">
    <div class="form-section-title">Datos Personales</div>

    <div class="form-row-2">
        <!-- Cédula -->
        <div class="form-group">
            <label for="cedula" class="form-label">
                Cédula de Ciudadanía
                <span class="required-star">*</span>
            </label>
            <div class="input-icon-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="4" width="18" height="16" rx="2"/>
                    <line x1="7" y1="8" x2="17" y2="8"/>
                    <line x1="7" y1="12" x2="13" y2="12"/>
                    <circle cx="16" cy="14" r="1.5"/>
                </svg>
                <input type="text" id="cedula" name="cedula" value="{{ old('cedula', $empleado->cedula) }}" placeholder="Ej. 1020304050" required>
            </div>
            @error('cedula')
                <div class="field-error-msg">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $message }}
                </div>
            @else
                <div class="form-helper">Documento de identidad único del empleado.</div>
            @enderror
        </div>

        <!-- Nombre -->
        <div class="form-group">
            <label for="nombre" class="form-label">
                Nombre Completo
                <span class="required-star">*</span>
            </label>
            <div class="input-icon-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
                <input type="text" id="nombre" name="nombre" value="{{ old('nombre', $empleado->nombre) }}" placeholder="Ej. Carlos Mendoza" required>
            </div>
            @error('nombre')
                <div class="field-error-msg">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <div class="form-section-title">Asignación Operativa</div>

    <div class="form-row-2">
        <!-- Cargo -->
        <div class="form-group">
            <label for="cargo" class="form-label">
                Cargo / Rol
                <span class="required-star">*</span>
            </label>
            <div class="input-icon-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="2" y="7" width="20" height="14" rx="2" ry="2"/>
                    <path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/>
                </svg>
                <select id="cargo" name="cargo" required>
                    <option value="">Seleccione un cargo…</option>
                    @foreach (\App\Models\Empleado::CARGOS as $cargo)
                        <option value="{{ $cargo }}" @selected(old('cargo', $empleado->cargo) === $cargo)>{{ $cargo }}</option>
                    @endforeach
                </select>
            </div>
            @error('cargo')
                <div class="field-error-msg">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>

        <!-- Turno -->
        <div class="form-group">
            <label for="turno" class="form-label">
                Turno Asignado
                <span class="required-star">*</span>
            </label>
            <div class="input-icon-wrapper">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <circle cx="12" cy="12" r="10"/>
                    <polyline points="12 6 12 12 16 14"/>
                </svg>
                <select id="turno" name="turno" required>
                    <option value="">Seleccione un turno…</option>
                    @foreach (\App\Models\Empleado::TURNOS as $turno)
                        <option value="{{ $turno }}" @selected(old('turno', $empleado->turno) === $turno)>{{ $turno }}</option>
                    @endforeach
                </select>
            </div>
            @error('turno')
                <div class="field-error-msg">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ $message }}
                </div>
            @enderror
        </div>
    </div>

    <!-- Torre Asignada -->
    <div class="form-group">
        <label for="torre_asignada" class="form-label">
            Torre Asignada
            <span class="required-star">*</span>
        </label>
        <div class="input-icon-wrapper">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M3 21h18M5 21V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16"/>
            </svg>
            <input type="text" id="torre_asignada" name="torre_asignada" value="{{ old('torre_asignada', $empleado->torre_asignada) }}" placeholder="Ej. A, B, C…" required>
        </div>
        @error('torre_asignada')
            <div class="field-error-msg">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                {{ $message }}
            </div>
        @else
            <div class="form-helper">Debe coincidir con la torre de los residentes para sincronizar en el dashboard (ej. A o B).</div>
        @enderror
    </div>

    <!-- Estado Activo Toggle -->
    <label class="switch-card" for="switch-activo">
        <div class="switch-content">
            <span class="switch-title">Empleado Activo</span>
            <span class="switch-desc">El colaborador estará activo en el sistema y visible en los cruces de turnos del dashboard.</span>
        </div>
        <div class="switch-toggle">
            <input type="checkbox" id="switch-activo" name="activo" value="1" @checked(old('activo', $empleado->activo ?? true))>
            <span class="slider"></span>
        </div>
    </label>

    <!-- Botones de Acción -->
    <div class="form-actions-bar">
        <a href="{{ route('empleados.index') }}" class="btn btn-outline">Cancelar</a>
        <button class="btn btn-grande" type="submit">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path>
                <polyline points="17 21 17 13 7 13 7 21"></polyline>
                <polyline points="7 3 7 8 15 8"></polyline>
            </svg>
            Guardar empleado
        </button>
    </div>
</div>