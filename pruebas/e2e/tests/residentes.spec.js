// Residentes: se crean por la API y se comprueba que la web los muestra.
const { test, expect } = require('@playwright/test');
const { iniciarSesion, crearResidente, borrarResidente } = require('./ayudas');

const residente = {
  cedula: '8880000001',
  nombre: 'Residente Playwright',
  torre: 'Q',
  apartamento: '501',
  deudaActual: 75000,
  tieneParqueadero: true,
};

test.beforeAll(async ({ request }) => {
  await crearResidente(request, residente);
});

test.afterAll(async ({ request }) => {
  await borrarResidente(request, residente.cedula);
});

test.beforeEach(async ({ page }) => {
  await iniciarSesion(page);
});

test('el dashboard muestra la torre y la deuda del residente', async ({ page }) => {
  await page.goto('/dashboard');

  const torre = page.locator('section', { hasText: 'Torre Q' });
  await expect(torre).toBeVisible();
  await expect(torre).toContainText('75.000');
});

test('la pantalla de residentes filtra por torre', async ({ page }) => {
  await page.goto('/residentes');
  await page.getByPlaceholder('Filtrar por torre').fill('Q');
  await page.getByRole('button', { name: 'Buscar' }).click();

  await expect(page).toHaveURL(/torre=Q/);
  await expect(page.getByText('Residente Playwright')).toBeVisible();
});
