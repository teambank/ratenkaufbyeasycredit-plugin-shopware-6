import template from './sw-order-user-card.html.twig';
import './sw-order-user-card.scss';

const { Component, Context } = Shopware;
const Criteria = Shopware.Data.Criteria;

const easycreditFormattedHandlerIdentifier = 'handler_netzkollektiv_handler';

Component.override('sw-order-user-card', {
    template,

    computed: {
        isEasyCreditPayment () {
            let tx = this.currentOrder.transactions;
            return tx.length == 1 && tx[0].paymentMethod.formattedHandlerIdentifier == easycreditFormattedHandlerIdentifier
        }
    }
});
