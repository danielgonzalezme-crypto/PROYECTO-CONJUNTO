// Configuración de las pruebas de extremo a extremo (E2E) con Playwright.
// Requisito: el sistema encendido con `docker compose up -d`.
const { defineConfig } = require('@playwright/test');
const { WEB_URL } = require('./tests/ayudas');

// Con el navegador visible (--headed) las pruebas van despacio, para poder seguirlas en una exposición.
// Se pasa por variable de entorno porque las pruebas corren en procesos aparte que la heredan.
if (process.argv.includes('--headed')) process.env.E2E_LENTO = '1';

module.exports = defineConfig({
  testDir: './tests',
  // Una prueba a la vez: todas usan la misma base de datos.
  workers: 1,
  timeout: 30_000,
  reporter: [['list'], ['html', { open: 'never' }]],
  use: {
    baseURL: WEB_URL,
    // En Windows usa Microsoft Edge, que ya viene instalado; en GitHub Actions, el Chromium de Playwright.
    channel: process.env.CI ? undefined : 'msedge',
    locale: 'es-CO',
    launchOptions: { slowMo: process.env.E2E_LENTO ? 600 : 0 },
    screenshot: 'only-on-failure',
    trace: 'retain-on-failure',
  },
});
