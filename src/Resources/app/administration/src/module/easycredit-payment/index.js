import './component/easycredit-tx-widget';
import './component/easycredit-intro';
import './component/easycredit-payment-status-select';
import './component/easycredit-order-status-select';
import './component/easycredit-test-credentials-button';
import './component/easycredit-click-and-collect-intro';
import './component/easycredit-marketing';

import './extension/sw-order-detail';
import './extension/sw-order-detail-base';
import './extension/sw-order-detail-details';
import './extension/sw-order-general-info';
import './extension/sw-order-user-card';

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
                path: '/sw/order/easycredit/detail/:id',
                meta: {
                    parentPath: 'sw.order.index',
                    privilege: 'order.viewer'
                }
            });
        }
        next(currentRoute);
    }
});
