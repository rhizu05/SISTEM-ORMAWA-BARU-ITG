import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './e2e',
  fullyParallel: false, // Set false untuk meminimalisir isu session database antar role
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: 1, // Pastikan dijalankan secara seri (serial)
  reporter: 'html',
  use: {
    baseURL: 'http://127.0.0.1:8000', // Gunakan localhost Artisan Serve
    trace: 'on-first-retry',
  },

  // Jalankan artisan serve secara otomatis sebelum test
  webServer: {
    command: 'php artisan serve',
    url: 'http://127.0.0.1:8000',
    reuseExistingServer: !process.env.CI,
  },

  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    }
  ],
});
