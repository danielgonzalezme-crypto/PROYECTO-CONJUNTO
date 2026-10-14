import { NextFunction, Request, RequestHandler, Response, Router } from "express";
import { propietarioService } from "./propietario.service";
import { actualizarPropietarioSchema, crearPropietarioSchema, validar } from "./validation";

// Envuelve los manejadores asíncronos para que sus errores lleguen al middleware de errores.
const asincrono =
  (manejador: (req: Request, res: Response, next: NextFunction) => Promise<unknown>): RequestHandler =>
  (req, res, next) => {
    manejador(req, res, next).catch(next);
  };

export const propietarioRoutes = Router();

propietarioRoutes.get(
  "/",
  asincrono(async (req, res) => {
    const tipo = typeof req.query.tipo === "string" ? req.query.tipo.toUpperCase() : undefined;
    res.json(await propietarioService.listar(tipo));
  }),
);

propietarioRoutes.get(
  "/:cedula",
  asincrono(async (req, res) => {
    res.json(await propietarioService.obtener(String(req.params.cedula)));
  }),
);

propietarioRoutes.post(
  "/",
  asincrono(async (req, res) => {
    const datos = validar(crearPropietarioSchema, req.body);
    res.status(201).json(await propietarioService.crear(datos));
  }),
);

propietarioRoutes.put(
  "/:cedula",
  asincrono(async (req, res) => {
    const datos = validar(actualizarPropietarioSchema, req.body);
    res.json(await propietarioService.actualizar(String(req.params.cedula), datos));
  }),
);

propietarioRoutes.delete(
  "/:cedula",
  asincrono(async (req, res) => {
    await propietarioService.eliminar(String(req.params.cedula));
    res.status(204).send();
  }),
);
