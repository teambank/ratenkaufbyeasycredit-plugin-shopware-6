import { test, expect } from '@playwright/test';

function delay(time) {
  return new Promise(function(resolve) {
      setTimeout(resolve, time)
  });
}

test.beforeEach(async ({page}, testInfo) => {
  await page.evaluate(() => {
    document.body.style.transform = 'scale(0.75)'
  })
})

test.beforeAll(async ({ request}, testInfo) => {
  var headers = {
    'Content-Type': 'application/json',
    Accept: 'application/json'
  }

  var response = await request.post('/api/oauth/token', {
    headers: headers,
    data: {
      "client_id": "administration",
      "grant_type": "password",
      "scopes": "write",
      "username": "admin",
      "password": "shopware"
    }
  });
  const authorization = await response.json()
  headers['Authorization'] = 'Bearer ' + authorization.access_token;

  response = await request.get('/api/sales-channel', {
    headers: headers
  })
  const salesChannel = await response.json().then((data) => {
    console.log(data);
    return data.data.find(e => e.name === 'Storefront')
  })

  response = await request.get('/api/tax', {
    headers: headers
  })
  const taxId = await response.json().then((data) => {
    return data.data.find(e => e.taxRate === 19).id
  })

  console.log({
    currencyId: salesChannel.currencyId,
    taxId: taxId,
    salesChannelId: salesChannel.id
  })

  var response = await request.post('/api/product', {
    headers: headers,
    data: {
      "name": "Product",
      "productNumber": "123456",
      "stock": 99999,
      "taxId": taxId,
      "price": [
        {
          "currencyId": salesChannel.currencyId,
          "gross": 201,
          "net": 200,
          "linked": false
        }
      ],
      "visibilities": [{
        "salesChannelId": salesChannel.id,
        "visibility": 30
      }],
      "categories": [
        {
        "displayNestedProducts": true,
        "type": "page",
        "productAssignmentType": "product",
        "name": "Home",
        "navigationSalesChannels": [{
          "id": salesChannel.id
        }]
        }
      ]
    }
  })

  response = await request.get('/api/product', {
    headers: headers
  })
  console.log(await response.json())

  
})
test.afterEach(async ({ page }, testInfo) => {
  if (testInfo.status !== testInfo.expectedStatus) {
    // Get a unique place for the screenshot.
    const screenshotPath = testInfo.outputPath(`failure.png`);
    // Add it to the report.
    testInfo.attachments.push({ name: 'screenshot', path: screenshotPath, contentType: 'image/png' });
    // Take the screenshot itself.
    await page.screenshot({ path: screenshotPath, timeout: 5000 });
  }
});

const goThroughPaymentPage = async (page, express: boolean = false) => {
  await test.step(`easyCredit-Ratenkauf Payment`, async() => {
    await page.getByTestId('uc-deny-all-button').click()
    await page.getByRole('button', { name: 'Weiter zur Dateneingabe' }).click()

    if (express) {
      await page.locator('#vorname').fill('Ralf');
      await page.locator('#nachname').fill('Ratenkauf');
    }

    await page.locator('#geburtsdatum').fill('18.03.1987')

    if (express) {
      await page.locator('#email').fill('ralf.ratenkauf@teambank.de')

    }
    await page.locator('#mobilfunknummer').fill('01703404848')
    await page.locator('#iban').fill('DE12120300000017576026')

    if (express) {
      await page.locator('#strasseHausNr').fill('Beuthener Str. 25')
      await page.locator('#plz').fill('90402')
      await page.locator('#ort').fill('Nürnberg')
    }

    await page.getByText('Allen zustimmen').click()

    await delay(500)
    await page.getByRole('button', { name: 'Ratenwunsch prüfen' }).click()

    await delay(500)
    await page.getByRole('button', { name: 'Ratenwunsch übernehmen' }).click()
  })
}

const confirmOrder = async (page) => {
  await test.step(`Confirm order`, async() => {
    /* Confirm Page */
    await delay(500)

    await expect(page.getByText('I have read')).toBeVisible()
    await page.evaluate(async() => {
      // workaround: checking checkboxes results in "Target closed" on CI
      document.getElementById('tos').checked = true
    })

    await page.getByRole('button', { name: 'Submit order' }).click()

    /* Success Page */
    await expect(page.getByText('Thank you for your order')).toBeVisible()
  })
}

const goToProduct = async (page, num = 0) => {
  await test.step(`Go to product (num: ${num}}`, async() => {
    await page.goto('/');
    await page.locator('nav a').nth(0).click() // click navigation
    await page.locator('a.product-name').nth(num).click()

     // try next product, if there is no express button
    //if ((await page.$('easycredit-express-button')) === null) {
    //  goToProduct(page, ++num)
    //}
  })
}

test('standardCheckout', async ({ page }) => {

  await goToProduct(page)

  await page.getByRole('button', { name: 'Add to shopping cart' }).first().click()
  await page.goto('/checkout/confirm')

  await page.getByRole('combobox', { name: /Salutation/ }).selectOption({ index: 1 })
  await page.getByRole('textbox', { name: 'First name*' }).fill('Ralf')
  await page.getByRole('textbox', { name: 'Last name*' }).fill('Ratenkauf')

  // SW 6.4
  // workaround: checking checkboxes results in "Target closed" on CI
  await page.click("text=Do not create a customer account")

  // SW 6.5
  //if (await page.locator('input[name="createCustomerAccount"]').isVisible()) {
  //  await page.locator('input[name="createCustomerAccount"]').uncheck()
  //}

  await page.getByLabel('Email address*').fill('test@email.com')
  // await page.getByLabel('Password*').fill('a12345678')

  
  await page.getByRole('textbox', { name: 'Street address*' }).fill('Beuthener Str. 25')
  await page.getByRole('textbox', { name: 'Postal code' }).fill('90402')
  await page.getByRole('textbox', { name: 'City*' }).fill('Nürnberg')
  await page.getByRole('combobox', { name: 'Country*' }).selectOption({ label: 'Germany' })

  await page.getByRole('button', { name: 'Continue' }).click()

  /* Confirm Page */
  await page.getByText('easyCredit-Ratenkauf').click()
  await page.getByRole('button', { name: 'Weiter zum Ratenkauf' }).click()
  await page.getByText('Akzeptieren', { exact: true }).click()

  await goThroughPaymentPage(page)
  await confirmOrder(page)
});

test('expressCheckout', async ({ page }) => {

  await goToProduct(page)

  await page.locator('a').filter({ hasText: 'Jetzt in Raten zahlen' }).click();
  await page.getByText('Akzeptieren', { exact: true }).click();

  await goThroughPaymentPage(page, true)
  await confirmOrder(page)
});
