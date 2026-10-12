import { z } from "zod";
import { ErrorCampo, HttpError } from "./errors";

const TIPOS = ["PROPIETARIO", "ARRENDATARIO"] as const;

// Los formularios envían cadenas vacías: se tratan como "sin dato".
const vacioANulo = (valor: unknown) => (valor === "" ? null : valor);

const telefono = (nombre: string) =>
  z.preprocess(
    vacioANulo,
    z
      .string()
      .trim()
      .regex(/^[0-9+() -]{7,20}$/, `${nombre} no es válido (7 a 20 dígitos, puede incluir + ( ) y guiones)`)
      .nullish(),
  );

const texto = (max: number, nombre: string) =>
  z.preprocess(
    vacioANulo,
    z.string().trim().max(max, `${nombre} no puede superar ${max} caracteres`).nullish(),
  );

const campos = {
  telefono: telefono("El teléfono"),
  correo: z.preprocess(
    vacioANulo,
    z.string().trim().max(120, "El correo no puede superar 120 caracteres").email("El correo no es válido").nullish(),
  ),
  tipoResidente: z.enum(TIPOS, { message: "tipoResidente debe ser PROPIETARIO o ARRENDATARIO" }),
  contactoEmergenciaNombre: texto(100, "El nombre del contacto de emergencia"),
  contactoEmergenciaTelefono: telefono("El teléfono del contacto de emergencia"),
  placaVehiculo: z.preprocess(
    vacioANulo,
    z
      .string()
      .trim()
      .toUpperCase()
      .regex(/^[A-Z0-9-]{5,8}$/, "La placa no es válida (5 a 8 caracteres, letras y números)")
      .nullish(),
  ),
  numeroParqueadero: texto(10, "El número de parqueadero"),
  tieneMascotas: z.boolean({ message: "tieneMascotas debe ser true o false" }).default(false),
  observaciones: texto(500, "Las observaciones"),
};

export const crearPropietarioSchema = z.object({
  cedula: z
    .string({ message: "La cédula es obligatoria" })
    .trim()
    .min(5, "La cédula debe tener al menos 5 caracteres")
    .max(20, "La cédula no puede superar 20 caracteres"),
  ...campos,
});

export const actualizarPropietarioSchema = z.object(campos);

export type DatosPropietario = z.infer<typeof actualizarPropietarioSchema>;
export type DatosNuevoPropietario = z.infer<typeof crearPropietarioSchema>;

export function validar<T>(schema: z.ZodType<T>, datos: unknown): T {
  const resultado = schema.safeParse(datos);
  if (resultado.success) {
    return resultado.data;
  }
  const errores: ErrorCampo[] = resultado.error.issues.map((issue) => ({
    campo: issue.path.join(".") || "(cuerpo)",
    mensaje: issue.message,
  }));
  throw new HttpError(400, "Datos inválidos", errores);
}
