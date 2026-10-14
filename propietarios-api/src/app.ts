import express, { NextFunction, Request, Response } from "express";
import { HttpError } from "./errors";
import { propietarioRoutes } from "./propietario.routes";

export const app = express();

app.disable("x-powered-by");
app.use(express.json({ limit: "100kb" }));

app.get("/health", (_req, res) => {
  res.json({ estado: "ok" });
});

app.use("/api/propietarios", propietarioRoutes);

app.use((req, res) => {
  res.status(404).json({ status: 404, detail: `Ruta no encontrada: ${req.method} ${req.path}` });
});

// Middleware de errores: todas las respuestas de error tienen el mismo formato JSON.
app.use((error: unknown, _req: Request, res: Response, _next: NextFunction) => {
  if (error instanceof HttpError) {
    res.status(error.status).json({ status: error.status, detail: error.message, errores: error.errores });
    return;
  }
  if ((error as { type?: string }).type === "entity.parse.failed") {
    res.status(400).json({ status: 400, detail: "El cuerpo de la petición no es un JSON válido" });
    return;
  }
  console.error(error);
  res.status(500).json({ status: 500, detail: "Error interno del servidor" });
});
