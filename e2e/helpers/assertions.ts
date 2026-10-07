import { Page, expect } from '@playwright/test';

export async function expectCalendarLocaleId(page: Page) {
  const calendar = page.locator('#calendar');
  await expect(calendar).toBeVisible();
  await page.waitForTimeout(600);
  await expect(page.locator('.fc-toolbar-title').first()).toContainText(/Januari|Februari|Maret|April|Mei|Juni|Juli|Agustus|September|Oktober|November|Desember/, { timeout: 7000 });
}

export async function expectNoRouteError(page: Page) {
  await expect(page.locator('text=RouteNotFoundException')).toHaveCount(0);
  await expect(page.locator('text=RelationNotFoundException')).toHaveCount(0);
  await expect(page.locator('text=BindingResolutionException')).toHaveCount(0);
}
