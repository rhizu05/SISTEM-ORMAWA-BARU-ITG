import { defineConfig, devices } from '@playwright/test';

const PORT = process.env.E2E_PORT || '8000';
const BASE_URL = process.env.E2E_BASE_URL || `http://127.0.0.1:${PORT}`;

export default defineConfig({
  testDir: './e2e',
  fullyParallel: false,
  forbidOnly: !!process.env.CI,
  retries: 2,
  workers: 1,
  reporter: 'html',
  timeout: 60000,
  expect: { timeout: 10000 },
  use: {
    baseURL: BASE_URL,
    trace: 'on-first-retry',
    actionTimeout: 10000,
    navigationTimeout: 30000,
  },

  webServer: {
    command: `php -S 127.0.0.1:${PORT} -t public`,
    url: BASE_URL,
    reuseExistingServer: false,
    timeout: 30000,
  },

  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    }
  ],
});
