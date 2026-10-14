import mysql from "mysql2/promise";
import { config } from "./config";

export const pool = mysql.createPool({
  host: config.db.host,
  port: config.db.port,
  database: config.db.database,
  user: config.db.user,
  password: config.db.password,
  waitForConnections: true,
  connectionLimit: 10,
  timezone: "Z",
});

const CREAR_TABLA = `
  CREATE TABLE IF NOT EXISTS propietarios (
    id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(20) NOT NULL,
    telefono VARCHAR(20) NULL,
    correo VARCHAR(120) NULL,
    tipo_residente ENUM('PROPIETARIO','ARRENDATARIO') NOT NULL,
    contacto_emergencia_nombre VARCHAR(100) NULL,
    contacto_emergencia_telefono VARCHAR(20) NULL,
    placa_vehiculo VARCHAR(8) NULL,
    numero_parqueadero VARCHAR(10) NULL,
    tiene_mascotas TINYINT(1) NOT NULL DEFAULT 0,
    observaciones VARCHAR(500) NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uk_propietarios_cedula (cedula)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
`;

const esperar = (ms: number) => new Promise((resolver) => setTimeout(resolver, ms));

/** Espera a que la base responda y crea la tabla si no existe. */
export async function iniciarBase(intentos = 30, pausaMs = 2000): Promise<void> {
  for (let intento = 1; intento <= intentos; intento++) {
    try {
      await pool.query("SELECT 1");
      await pool.query(CREAR_TABLA);
      console.log("Base de datos lista");
      return;
    } catch (error) {
      console.log(`Esperando la base de datos (${intento}/${intentos})...`);
      if (intento === intentos) {
        throw error;
      }
      await esperar(pausaMs);
    }
  }
}
