// Parqueaderos: la tabla y la consulta en vivo cruzan la API de Java (cupo) con la de Node (bahía y placa).
const { test, expect } = require('@playwright/test');
const { iniciarSesion, crearResidente, borrarResidente } = require('./ayudas');

const conVehiculo = {
  residente: { cedula: '8880000004', nombre: 'Valeria Parqueadero Prueba', torre: 'Q', apartamento: '504', deudaActual: 120000, tieneParqueadero: true },
  propietario: { telefono: '3109876543', tipoResidente: 'ARRENDATARIO', numeroParqueadero: 'P-98', placaVehiculo: 'PWT456' },
};

test.beforeAll(async ({ request }) => {
  await crearResidente(request, conVehiculo.residente, conVehiculo.propietario);
});

test.afterAll(async ({ request }) => {
  await borrarResidente(request, conVehiculo.residente.cedula);
});

test.beforeEach(async ({ page }) => {
  await iniciarSesion(page);
  await page.goto('/parqueaderos');
});

test('se llega desde el menú y muestra el título', async ({ page }) => {
  await page.goto('/dashboard');
  await page.getByRole('link', { name: 'Parqueaderos' }).click();

  await expect(page).toHaveURL(/\/parqueaderos$/);
  await expect(page.getByRole('heading', { name: /Control de Parqueaderos/ })).toBeVisible();
});

test('la tabla cruza el cupo de Java con la bahía y la placa de Node', async ({ page }) => {
  const fila = page.locator('tr', { hasText: 'Valeria Parqueadero Prueba' });

  await expect(fila).toContainText('Asignado');
  await expect(fila).toContainText('P-98');
  await expect(fila).toContainText('PWT456');
  await expect(fila).toContainText('ARRENDATARIO');
  await expect(fila).toContainText('Torre Q - 504');
});

test('la consulta en vivo muestra los datos de las dos APIs', async ({ page }) => {
  await page.locator('#cedulaInput').fill('8880000004');
  await page.locator('#btnConsultarLive').click();

  await expect(page.getByText('¡Datos recibidos exitosamente de los microservicios para cédula 8880000004!')).toBeVisible();
  await expect(page.locator('#cardJavaNombre')).toHaveText('Valeria Parqueadero Prueba');
  await expect(page.locator('#cardJavaUbicacion')).toHaveText('Torre Q · Apartamento 504');
  await expect(page.locator('#cardNodeBahia')).toContainText('P-98');
  await expect(page.locator('#cardNodePlaca')).toContainText('PWT456');
  await expect(page.locator('#cardNodeTipo')).toHaveText('ARRENDATARIO');
});

test('la consulta en vivo también funciona con la tecla Enter', async ({ page }) => {
  await page.locator('#cedulaInput').fill('8880000004');
  await page.locator('#cedulaInput').press('Enter');

  await expect(page.locator('#cardJavaNombre')).toHaveText('Valeria Parqueadero Prueba');
});

test('una cédula que no existe lo informa sin mostrar tarjetas', async ({ page }) => {
  await page.locator('#cedulaInput').fill('0000000000');
  await page.locator('#btnConsultarLive').click();

  await expect(page.getByText('No se encontró ningún registro para la cédula 0000000000 en Java ni en Node.js.')).toBeVisible();
  await expect(page.locator('#liveResultContainer')).toBeHidden();
});

test('consultar con la cédula vacía pide escribirla', async ({ page }) => {
  await page.locator('#cedulaInput').fill('');
  await page.locator('#btnConsultarLive').click();

  await expect(page.getByText('Por favor escribe un número de cédula para consultar.')).toBeVisible();
});
