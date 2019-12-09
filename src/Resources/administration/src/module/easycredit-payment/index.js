import './extension/sw-order';
import './page/easycredit-payment-detail';

import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

const { Module } = Shopware;

Module.register('easycredit-payment', {
    type: 'plugin',
    name: 'EasyCreditRatenkauf',
    description: 'easycredit-payment.general.descriptionTextModule',
    version: '1.0.0',
    targetVersion: '1.0.0',
    color: '#2b52ff',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routeMiddleware(next, currentRoute) {
        if (currentRoute.name === 'sw.order.detail') {
            currentRoute.children.push({
                component: 'easycredit-payment-detail',
                name: 'swag.easycredit.payment.detail',
                isChildren: true,
                path: '/sw/order/easycredit/detail/:id'
            });
        }
        next(currentRoute);
    }
});
