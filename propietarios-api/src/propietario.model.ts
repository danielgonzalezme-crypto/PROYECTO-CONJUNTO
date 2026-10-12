import type { RowDataPacket } from "mysql2";

export interface Propietario {
  id: number;
  cedula: string;
  telefono: string | null;
  correo: string | null;
  tipoResidente: "PROPIETARIO" | "ARRENDATARIO";
  contactoEmergenciaNombre: string | null;
  contactoEmergenciaTelefono: string | null;
  placaVehiculo: string | null;
  numeroParqueadero: string | null;
  tieneMascotas: boolean;
  observaciones: string | null;
  creadoEn: string;
  actualizadoEn: string;
}

export interface FilaPropietario extends RowDataPacket {
  id: number;
  cedula: string;
  telefono: string | null;
  correo: string | null;
  tipo_residente: "PROPIETARIO" | "ARRENDATARIO";
  contacto_emergencia_nombre: string | null;
  contacto_emergencia_telefono: string | null;
  placa_vehiculo: string | null;
  numero_parqueadero: string | null;
  tiene_mascotas: number;
  observaciones: string | null;
  created_at: Date;
  updated_at: Date;
}

/** Convierte una fila de la base (snake_case) al contrato JSON (camelCase). */
export function aPropietario(fila: FilaPropietario): Propietario {
  return {
    id: fila.id,
    cedula: fila.cedula,
    telefono: fila.telefono,
    correo: fila.correo,
    tipoResidente: fila.tipo_residente,
    contactoEmergenciaNombre: fila.contacto_emergencia_nombre,
    contactoEmergenciaTelefono: fila.contacto_emergencia_telefono,
    placaVehiculo: fila.placa_vehiculo,
    numeroParqueadero: fila.numero_parqueadero,
    tieneMascotas: Boolean(fila.tiene_mascotas),
    observaciones: fila.observaciones,
    creadoEn: new Date(fila.created_at).toISOString(),
    actualizadoEn: new Date(fila.updated_at).toISOString(),
  };
}
