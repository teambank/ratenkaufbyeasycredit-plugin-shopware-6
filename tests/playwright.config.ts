import {
  PlaywrightTestConfig,
  Project,
  defineConfig,
  devices,
} from "@playwright/test";
import { seconds } from "./utils";

let projects: Project[] = [
  { name: `backend-auth`, testMatch: /.*\.setup\.ts/ },
];

[
  "Desktop Chrome"
  //"iPhone 14"
].forEach((device) => {
  projects.push({
    name: `checkout @${device}`,
    use: {
      ...devices[device],
    },
    testMatch: "checkout.spec.ts",
  });
});

/* test backend only desktop */
["Desktop Chrome"].forEach((device) => {
  let name = projects.find((p) => p.name?.match("checkout"))?.name; // checkout required, so that we have at least one order in the backend
  projects.push({
    name: `backend @${device}`,
    use: {
      ...devices[device],
      storageState: "playwright/.auth/user.json",
    },
    dependencies: [`backend-auth`, name as string],
    testMatch: "backend.spec.ts",
  });
});

let config: PlaywrightTestConfig = {
  outputDir: "../test-results/" + process.env.VERSION + "/",
  use: {
    baseURL: process.env.BASE_URL ?? "http://localhost",
    trace: "retain-on-failure",
    locale: "de-DE",
  },
  retries: process.env.CI ? 2 : 0,
  timeout: seconds(30),
  projects: projects,
  reporter: [
    ["list", { printSteps: true }],
    ["html" ],
  ],
};

export default defineConfig(config);
