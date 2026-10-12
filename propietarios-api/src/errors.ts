export interface ErrorCampo {
  campo: string;
  mensaje: string;
}

export class HttpError extends Error {
  constructor(
    public readonly status: number,
    message: string,
    public readonly errores?: ErrorCampo[],
  ) {
    super(message);
    this.name = "HttpError";
  }
}

export const noEncontrado = (cedula: string): HttpError =>
  new HttpError(404, `No existe un propietario con cédula ${cedula}`);

export const duplicado = (cedula: string): HttpError =>
  new HttpError(409, `Ya existe un propietario con cédula ${cedula}`);
