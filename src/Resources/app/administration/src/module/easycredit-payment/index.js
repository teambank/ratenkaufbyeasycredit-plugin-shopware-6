import './extension/sw-order';
import './page/easycredit-payment-detail';

const { Module } = Shopware;

Module.register('easycredit-payment', {
    type: 'plugin',
    name: 'EasyCreditRatenkauf',
    description: 'easycredit-payment.general.descriptionTextModule',
    version: '1.0.0',
    targetVersion: '1.0.0',
    color: '#2b52ff',

    routeMiddleware(next, currentRoute) {
        if (currentRoute.name === 'sw.order.detail') {
            currentRoute.children.push({
                component: 'easycredit-payment-detail',
                name: 'netzkollektiv.easycredit.payment.detail',
                isChildren: true,
                path: '/sw/order/easycredit/detail/:id'
            });
        }
        next(currentRoute);
    }
});
