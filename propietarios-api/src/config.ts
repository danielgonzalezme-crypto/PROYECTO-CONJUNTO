function entero(valor: string | undefined, porDefecto: number): number {
  const n = Number(valor);
  return Number.isInteger(n) && n > 0 ? n : porDefecto;
}

export const config = {
  port: entero(process.env.PORT, 3000),
  // Por defecto solo escucha en este equipo; en Docker se cambia a 0.0.0.0 desde el compose.
  host: process.env.HOST ?? "127.0.0.1",
  db: {
    host: process.env.DB_HOST ?? "127.0.0.1",
    port: entero(process.env.DB_PORT, 3310),
    database: process.env.DB_NAME ?? "propietarios_db",
    user: process.env.DB_USER ?? "propietarios_user",
    password: process.env.DB_PASSWORD ?? "",
  },
};
