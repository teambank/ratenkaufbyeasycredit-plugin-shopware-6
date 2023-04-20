import { defineConfig } from '@playwright/test';

export default defineConfig({
  outputDir: '../test-results/'+ process.env.VERSION + '/',
  use: {
    baseURL: process.env.BASE_URL ?? 'http://localhost',
    trace: 'on'
  },
  timeout: 5000 // 5 * 60 * 1000, // 5m
});
