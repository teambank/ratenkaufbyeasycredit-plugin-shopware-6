import template from './sw-order-general-info.html.twig';

const { Component } = Shopware;

const easycreditFormattedHandlerIdentifier = 'handler_netzkollektiv_handler';

Component.override('sw-order-general-info', {
    template,

    computed: {
        isEasyCreditPayment () {
            let tx = this.order.transactions;
            return tx.length == 1 && tx[0].paymentMethod.formattedHandlerIdentifier == easycreditFormattedHandlerIdentifier
        }
    }
});
