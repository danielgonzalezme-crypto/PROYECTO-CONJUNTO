// Gestión de empleados de principio a fin: crear, editar y eliminar desde la interfaz.
const { test, expect } = require('@playwright/test');
const { iniciarSesion } = require('./ayudas');

test.beforeEach(async ({ page }) => {
  await iniciarSesion(page);
});

test('crear, editar y eliminar un empleado', async ({ page }) => {
  const cedula = 'E2E' + Date.now();
  const nombre = `Prueba Playwright ${cedula.slice(-4)}`;

  // Crear
  await page.goto('/empleados/create');
  await page.locator('#cedula').fill(cedula);
  await page.locator('#nombre').fill(nombre);
  await page.locator('#cargo').selectOption('Vigilante');
  await page.locator('#turno').selectOption('Noche');
  await page.locator('#torre_asignada').fill('A');
  await page.locator('form[action*="/empleados"] button[type=submit]').click();

  await expect(page.getByText('Empleado creado.')).toBeVisible();
  const fila = page.locator('tr', { hasText: nombre });
  await expect(fila).toBeVisible();

  // Editar: cambia el turno
  await fila.locator('.btn-action-edit').click();
  await page.locator('#turno').selectOption('Tarde');
  await page.locator('form[action*="/empleados"] button[type=submit]').click();

  await expect(page.getByText('Empleado actualizado.')).toBeVisible();
  await expect(page.locator('tr', { hasText: nombre })).toContainText('Tarde');

  // Eliminar: acepta el mensaje de confirmación del navegador
  page.once('dialog', (dialogo) => dialogo.accept());
  await page.locator('tr', { hasText: nombre }).locator('.btn-action-delete').click();

  await expect(page.getByText('Empleado eliminado.')).toBeVisible();
  await expect(page.locator('tr', { hasText: nombre })).toHaveCount(0);
});

test('el formulario vacío muestra los errores en español', async ({ page }) => {
  await page.goto('/empleados/create');
  // Se quitan las validaciones del navegador para probar las del servidor.
  await page.evaluate(() => document.querySelectorAll('[required]').forEach((campo) => campo.removeAttribute('required')));
  await page.locator('form[action*="/empleados"] button[type=submit]').click();

  await expect(page.getByText('El campo cédula es obligatorio.')).toBeVisible();
  await expect(page.getByText('El campo nombre es obligatorio.')).toBeVisible();
});
