import { app } from "./app";
import { config } from "./config";
import { iniciarBase } from "./db";

async function main(): Promise<void> {
  if (!config.db.password) {
    console.warn("Aviso: DB_PASSWORD está vacío");
  }
  await iniciarBase();
  app.listen(config.port, config.host, () => {
    console.log(`propietarios-api escuchando en http://${config.host}:${config.port}`);
  });
}

main().catch((error) => {
  console.error("No se pudo iniciar la API:", error);
  process.exit(1);
});
