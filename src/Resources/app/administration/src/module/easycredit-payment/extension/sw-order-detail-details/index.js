import template from './sw-order-detail-details.html.twig';

const { Component, Context } = Shopware;
const Criteria = Shopware.Data.Criteria;

const easycreditFormattedHandlerIdentifier = 'handler_netzkollektiv_handler';

Component.override('sw-order-detail-details', {
    template,

    computed: {
        isEasyCreditPayment () {
            let tx = this.order.transactions;
            return tx.length == 1 && tx[0].paymentMethod.formattedHandlerIdentifier == easycreditFormattedHandlerIdentifier
        }
    }
});
