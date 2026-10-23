// Búsqueda de residentes: une la API de Java (residentes) con la de Node (contacto)
// y permite buscar por nombre, cédula, teléfono o correo.
const { test, expect } = require('@playwright/test');
const { iniciarSesion, crearResidente, borrarResidente } = require('./ayudas');

const conContacto = {
  residente: { cedula: '8880000002', nombre: 'Rocío Peñaranda Prueba', torre: 'Q', apartamento: '502', deudaActual: 0, tieneParqueadero: true },
  propietario: { telefono: '300 123 4567', correo: 'rocio.playwright@ejemplo.test', tipoResidente: 'PROPIETARIO', numeroParqueadero: 'P-99', placaVehiculo: 'PWR123' },
};
const sinContacto = {
  residente: { cedula: '8880000003', nombre: 'Tomás Sincontacto Prueba', torre: 'Q', apartamento: '503', deudaActual: 0, tieneParqueadero: false },
};

test.beforeAll(async ({ request }) => {
  await crearResidente(request, conContacto.residente, conContacto.propietario);
  await crearResidente(request, sinContacto.residente);
});

test.afterAll(async ({ request }) => {
  await borrarResidente(request, conContacto.residente.cedula);
  await borrarResidente(request, sinContacto.residente.cedula);
});

test.beforeEach(async ({ page }) => {
  await iniciarSesion(page);
  await page.goto('/residentes');
});

async function buscar(page, texto) {
  await page.getByPlaceholder('Buscar por nombre, cédula, teléfono o correo').fill(texto);
  await page.getByRole('button', { name: 'Buscar' }).click();
}

const fila = (page, nombre) => page.locator('tr', { hasText: nombre });

test('busca por parte del nombre, sin importar tildes ni mayúsculas', async ({ page }) => {
  await buscar(page, 'rocio penaranda');

  await expect(fila(page, 'Rocío Peñaranda Prueba')).toBeVisible();
  await expect(fila(page, 'Tomás Sincontacto Prueba')).toHaveCount(0);
});

test('busca por cédula', async ({ page }) => {
  await buscar(page, '8880000003');

  await expect(fila(page, 'Tomás Sincontacto Prueba')).toBeVisible();
  await expect(fila(page, 'Rocío Peñaranda Prueba')).toHaveCount(0);
});

test('busca por teléfono aunque se escriba con otro formato', async ({ page }) => {
  await buscar(page, '3001234567');

  await expect(fila(page, 'Rocío Peñaranda Prueba')).toBeVisible();
});

test('busca por correo y muestra los datos de contacto de la API de Node', async ({ page }) => {
  await buscar(page, 'rocio.playwright@');

  const resultado = fila(page, 'Rocío Peñaranda Prueba');
  await expect(resultado).toContainText('300 123 4567');
  await expect(resultado).toContainText('rocio.playwright@ejemplo.test');
});

test('un residente sin ficha en la API de Node aparece sin teléfono ni correo', async ({ page }) => {
  await buscar(page, 'Sincontacto');

  await expect(fila(page, 'Tomás Sincontacto Prueba')).toContainText('—');
});

test('una búsqueda sin coincidencias lo dice claramente', async ({ page }) => {
  await buscar(page, 'nadie-se-llama-asi');

  await expect(page.getByText('No hay residentes para mostrar.')).toBeVisible();
});

test('el parqueadero del resultado lleva a su ficha en Parqueaderos', async ({ page }) => {
  await buscar(page, '8880000002');
  await fila(page, 'Rocío Peñaranda Prueba').getByRole('link', { name: /P-99/ }).click();

  await expect(page).toHaveURL(/\/parqueaderos\?cedula=8880000002/);
});
