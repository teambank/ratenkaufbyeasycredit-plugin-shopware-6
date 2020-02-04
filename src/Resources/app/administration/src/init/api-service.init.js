import EasyCreditRatenkaufApiCredentialsService
    from '../../src/core/service/api/easycredit-api-credentials.service';
import EasyCreditRatenkaufPaymentService
    from '../../src/core/service/api/easycredit-payment.service';

const { Application } = Shopware;

Application.addServiceProvider('EasyCreditRatenkaufApiCredentialsService', (container) => {
    const initContainer = Application.getContainer('init');

    return new EasyCreditRatenkaufApiCredentialsService(initContainer.httpClient, container.loginService);
});

Application.addServiceProvider('EasyCreditRatenkaufPaymentService', (container) => {
    const initContainer = Application.getContainer('init');

    return new EasyCreditRatenkaufPaymentService(initContainer.httpClient, container.loginService);
});