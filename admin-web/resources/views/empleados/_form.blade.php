<label for="cedula">Cédula</label>
<input type="text" id="cedula" name="cedula" value="{{ old('cedula', $empleado->cedula) }}" required>
@error('cedula') <small class="error">{{ $message }}</small> @enderror

<label for="nombre">Nombre</label>
<input type="text" id="nombre" name="nombre" value="{{ old('nombre', $empleado->nombre) }}" required>
@error('nombre') <small class="error">{{ $message }}</small> @enderror

<label for="cargo">Cargo</label>
<select id="cargo" name="cargo" required>
    <option value="">Seleccione…</option>
    @foreach (\App\Models\Empleado::CARGOS as $cargo)
        <option value="{{ $cargo }}" @selected(old('cargo', $empleado->cargo) === $cargo)>{{ $cargo }}</option>
    @endforeach
</select>
@error('cargo') <small class="error">{{ $message }}</small> @enderror

<label for="turno">Turno</label>
<select id="turno" name="turno" required>
    <option value="">Seleccione…</option>
    @foreach (\App\Models\Empleado::TURNOS as $turno)
        <option value="{{ $turno }}" @selected(old('turno', $empleado->turno) === $turno)>{{ $turno }}</option>
    @endforeach
</select>
@error('turno') <small class="error">{{ $message }}</small> @enderror

<label for="torre_asignada">Torre asignada</label>
<input type="text" id="torre_asignada" name="torre_asignada" value="{{ old('torre_asignada', $empleado->torre_asignada) }}" required>
@error('torre_asignada') <small class="error">{{ $message }}</small> @enderror

<label>
    <input type="checkbox" name="activo" value="1" @checked(old('activo', $empleado->activo ?? true))>
    Activo
</label>

<div class="acciones">
    <button class="btn btn-grande" type="submit">Guardar</button>
    <a href="{{ route('empleados.index') }}">Cancelar</a>
</div>