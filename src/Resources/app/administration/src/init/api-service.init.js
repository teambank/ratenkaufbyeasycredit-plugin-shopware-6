import EasyCreditRatenkaufApiCredentialsService
    from '../core/service/api/easycredit-api-credentials.service';

const { Application } = Shopware;

Application.addServiceProvider('EasyCreditRatenkaufApiCredentialsService', (container) => {
    const initContainer = Application.getContainer('init');

    return new EasyCreditRatenkaufApiCredentialsService(initContainer.httpClient, container.loginService);
});
