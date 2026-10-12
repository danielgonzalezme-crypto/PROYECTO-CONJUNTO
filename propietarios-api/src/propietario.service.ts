import { duplicado, HttpError, noEncontrado } from "./errors";
import type { Propietario } from "./propietario.model";
import { propietarioRepository } from "./propietario.repository";
import type { DatosNuevoPropietario, DatosPropietario } from "./validation";

// Código de error de MySQL para "entrada duplicada" (clave única).
const ERROR_DUPLICADO = "ER_DUP_ENTRY";

async function obtener(cedula: string): Promise<Propietario> {
  const propietario = await propietarioRepository.buscarPorCedula(cedula);
  if (!propietario) {
    throw noEncontrado(cedula);
  }
  return propietario;
}

export const propietarioService = {
  async listar(tipo?: string): Promise<Propietario[]> {
    if (tipo && tipo !== "PROPIETARIO" && tipo !== "ARRENDATARIO") {
      throw new HttpError(400, "El filtro tipo debe ser PROPIETARIO o ARRENDATARIO");
    }
    return propietarioRepository.listar(tipo);
  },

  obtener,

  async crear(datos: DatosNuevoPropietario): Promise<Propietario> {
    if (await propietarioRepository.buscarPorCedula(datos.cedula)) {
      throw duplicado(datos.cedula);
    }
    try {
      await propietarioRepository.crear(datos);
    } catch (error) {
      // Dos peticiones simultáneas pueden pasar la comprobación anterior; la base lo impide.
      if ((error as { code?: string }).code === ERROR_DUPLICADO) {
        throw duplicado(datos.cedula);
      }
      throw error;
    }
    return obtener(datos.cedula);
  },

  async actualizar(cedula: string, datos: DatosPropietario): Promise<Propietario> {
    await obtener(cedula);
    await propietarioRepository.actualizar(cedula, datos);
    return obtener(cedula);
  },

  async eliminar(cedula: string): Promise<void> {
    if (!(await propietarioRepository.eliminar(cedula))) {
      throw noEncontrado(cedula);
    }
  },
};
