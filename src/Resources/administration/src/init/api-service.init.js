import NetzkollektivEasyCreditApiCredentialsService
    from '../../src/core/service/api/easycredit-api-credentials.service';
import NetzkollektivEasyCreditPaymentService
    from '../../src/core/service/api/easycredit-payment.service';

const { Application } = Shopware;

Application.addServiceProvider('NetzkollektivEasyCreditApiCredentialsService', (container) => {
    const initContainer = Application.getContainer('init');

    return new NetzkollektivEasyCreditApiCredentialsService(initContainer.httpClient, container.loginService);
});

Application.addServiceProvider('NetzkollektivEasyCreditPaymentService', (container) => {
    const initContainer = Application.getContainer('init');

    return new NetzkollektivEasyCreditPaymentService(initContainer.httpClient, container.loginService);
});