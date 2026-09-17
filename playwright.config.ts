import { defineConfig, devices } from '@playwright/test';

const PORT = process.env.E2E_PORT || '8000';
const BASE_URL =
  process.env.E2E_BASE_URL || `http://127.0.0.1:${PORT}`;

export default defineConfig({
  testDir: './e2e',

  // Test dijalankan secara berurutan
  fullyParallel: false,
  workers: 1,

  // CI: fail kalau ada test.only
  forbidOnly: !!process.env.CI,

  // Retry hanya berguna ketika test gagal
  retries: process.env.CI ? 2 : 0,

  // Report
  reporter: [
    ['list'],
    ['html', { open: 'always' }],
  ],

  // Global timeout
  timeout: 60_000,

  // expect(locator).toBe...()
  expect: {
    timeout: 10_000,
  },

  use: {
    baseURL: BASE_URL,

    // Debugging failure
    trace: 'retain-on-failure',
    screenshot: 'only-on-failure',
    video: 'retain-on-failure',

    actionTimeout: 10_000,
    navigationTimeout: 30_000,
  },

  webServer: {
    command: `php -S 127.0.0.1:${PORT} -t public`,
    url: BASE_URL,
    reuseExistingServer: false,
    timeout: 30_000,
  },

  projects: [
    {
      name: 'chromium',
      use: {
        ...devices['Desktop Chrome'],
      },
    },
  ],
});