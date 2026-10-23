// Datos y pasos compartidos por todas las pruebas.
const fs = require('fs');
const path = require('path');
const { expect } = require('@playwright/test');

const RAIZ = path.resolve(__dirname, '..', '..', '..');

// Puertos de las APIs: los mismos del archivo .env del proyecto.
function variableEnv(nombre, porDefecto) {
  try {
    const env = fs.readFileSync(path.join(RAIZ, '.env'), 'utf8');
    const m = env.match(new RegExp(`^\\s*${nombre}\\s*=\\s*(\\d+)`, 'm'));
    if (m) return m[1];
  } catch { /* sin .env: se usa el valor por defecto */ }
  return porDefecto;
}

const WEB_URL = process.env.WEB_URL || 'http://127.0.0.1:8000';
const API_URL = process.env.API_URL || `http://127.0.0.1:${variableEnv('API_PORT', '8080')}/api/residentes`;
const PROPIETARIOS_URL = process.env.PROPIETARIOS_URL
  || `http://127.0.0.1:${variableEnv('PROPIETARIOS_API_PORT', '3000')}/api/propietarios`;

// El usuario de demostración se lee del seeder del proyecto, para no repetir la contraseña aquí.
function usuarioDemo() {
  const seeder = fs.readFileSync(path.join(RAIZ, 'admin-web/database/seeders/DatabaseSeeder.php'), 'utf8');
  return {
    correo: seeder.match(/ADMIN_EMAIL = '([^']+)'/)[1],
    clave: seeder.match(/ADMIN_PASSWORD = '([^']+)'/)[1],
  };
}

async function iniciarSesion(page) {
  const { correo, clave } = usuarioDemo();
  await page.goto('/login');
  await page.getByLabel('Correo').fill(correo);
  await page.getByLabel('Contraseña').fill(clave);
  await page.getByRole('button', { name: 'Entrar' }).click();
  await page.waitForURL('**/dashboard');
}

// Crea un residente en la API de Java (y su ficha en la API de Node, si se pasa) borrando antes
// cualquier resto de una ejecución anterior.
async function crearResidente(request, residente, propietario) {
  await borrarResidente(request, residente.cedula);
  expect((await request.post(API_URL, { data: residente })).status()).toBe(201);
  if (propietario) {
    expect((await request.post(PROPIETARIOS_URL, { data: { cedula: residente.cedula, ...propietario } })).status()).toBe(201);
  }
}

async function borrarResidente(request, cedula) {
  await request.delete(`${API_URL}/${cedula}`);
  await request.delete(`${PROPIETARIOS_URL}/${cedula}`);
}

module.exports = { WEB_URL, API_URL, PROPIETARIOS_URL, usuarioDemo, iniciarSesion, crearResidente, borrarResidente };
