// Inicio y cierre de sesión, tal como lo haría una persona en el navegador.
const { test, expect } = require('@playwright/test');
const { iniciarSesion } = require('./ayudas');

test('sin iniciar sesión, el dashboard redirige al login', async ({ page }) => {
  await page.goto('/dashboard');

  await expect(page).toHaveURL(/\/login$/);
  await expect(page.getByRole('heading', { name: 'Iniciar sesión' })).toBeVisible();
});

test('con una contraseña incorrecta no deja entrar', async ({ page }) => {
  await page.goto('/login');
  // Correo ficticio y distinto en cada ejecución: así el bloqueo por intentos fallidos
  // no afecta al usuario de demostración ni a la siguiente corrida.
  await page.getByLabel('Correo').fill(`intruso${Date.now()}@ejemplo.test`);
  await page.getByLabel('Contraseña').fill('incorrecta');
  await page.getByRole('button', { name: 'Entrar' }).click();

  await expect(page.getByText('El correo o la contraseña no son correctos.')).toBeVisible();
  await expect(page).toHaveURL(/\/login$/);
});

test('con el usuario correcto entra al dashboard', async ({ page }) => {
  await iniciarSesion(page);

  await expect(page.getByRole('heading', { name: 'Resumen por torre' })).toBeVisible();
});

test('cerrar sesión vuelve al login', async ({ page }) => {
  await iniciarSesion(page);
  await page.getByTitle('Cerrar sesión').click();

  await expect(page).toHaveURL(/\/login$/);
  // Ya sin sesión, el dashboard vuelve a pedir login.
  await page.goto('/dashboard');
  await expect(page).toHaveURL(/\/login$/);
});
