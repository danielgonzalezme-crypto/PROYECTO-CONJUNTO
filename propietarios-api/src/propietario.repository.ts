import type { ResultSetHeader } from "mysql2";
import { pool } from "./db";
import { aPropietario, FilaPropietario, Propietario } from "./propietario.model";
import type { DatosNuevoPropietario, DatosPropietario } from "./validation";

const COLUMNAS = `id, cedula, telefono, correo, tipo_residente,
  contacto_emergencia_nombre, contacto_emergencia_telefono,
  placa_vehiculo, numero_parqueadero, tiene_mascotas, observaciones,
  created_at, updated_at`;

// mysql2 no acepta "undefined" como parámetro: todo lo ausente se guarda como NULL.
const nulo = <T>(valor: T | null | undefined): T | null => valor ?? null;

export const propietarioRepository = {
  async listar(tipo?: string): Promise<Propietario[]> {
    const [filas] = tipo
      ? await pool.execute<FilaPropietario[]>(
          `SELECT ${COLUMNAS} FROM propietarios WHERE tipo_residente = ? ORDER BY cedula`,
          [tipo],
        )
      : await pool.execute<FilaPropietario[]>(`SELECT ${COLUMNAS} FROM propietarios ORDER BY cedula`);
    return filas.map(aPropietario);
  },

  async buscarPorCedula(cedula: string): Promise<Propietario | null> {
    const [filas] = await pool.execute<FilaPropietario[]>(
      `SELECT ${COLUMNAS} FROM propietarios WHERE cedula = ?`,
      [cedula],
    );
    return filas.length > 0 ? aPropietario(filas[0]) : null;
  },

  async crear(datos: DatosNuevoPropietario): Promise<void> {
    await pool.execute<ResultSetHeader>(
      `INSERT INTO propietarios
        (cedula, telefono, correo, tipo_residente, contacto_emergencia_nombre,
         contacto_emergencia_telefono, placa_vehiculo, numero_parqueadero,
         tiene_mascotas, observaciones)
       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      [
        datos.cedula,
        nulo(datos.telefono),
        nulo(datos.correo),
        datos.tipoResidente,
        nulo(datos.contactoEmergenciaNombre),
        nulo(datos.contactoEmergenciaTelefono),
        nulo(datos.placaVehiculo),
        nulo(datos.numeroParqueadero),
        datos.tieneMascotas ? 1 : 0,
        nulo(datos.observaciones),
      ],
    );
  },

  async actualizar(cedula: string, datos: DatosPropietario): Promise<void> {
    await pool.execute<ResultSetHeader>(
      `UPDATE propietarios SET
         telefono = ?, correo = ?, tipo_residente = ?, contacto_emergencia_nombre = ?,
         contacto_emergencia_telefono = ?, placa_vehiculo = ?, numero_parqueadero = ?,
         tiene_mascotas = ?, observaciones = ?
       WHERE cedula = ?`,
      [
        nulo(datos.telefono),
        nulo(datos.correo),
        datos.tipoResidente,
        nulo(datos.contactoEmergenciaNombre),
        nulo(datos.contactoEmergenciaTelefono),
        nulo(datos.placaVehiculo),
        nulo(datos.numeroParqueadero),
        datos.tieneMascotas ? 1 : 0,
        nulo(datos.observaciones),
        cedula,
      ],
    );
  },

  async eliminar(cedula: string): Promise<boolean> {
    const [resultado] = await pool.execute<ResultSetHeader>(
      "DELETE FROM propietarios WHERE cedula = ?",
      [cedula],
    );
    return resultado.affectedRows > 0;
  },
};
