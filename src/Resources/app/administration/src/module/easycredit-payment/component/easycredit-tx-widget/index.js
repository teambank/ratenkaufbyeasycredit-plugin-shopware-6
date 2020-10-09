import template from './easycredit-tx-widget.html.twig';
import './easycredit-tx-widget.scss';

const { Component, Context } = Shopware;

const easycreditFormattedHandlerIdentifier = 'handler_netzkollektiv_handler';

Component.register('easycredit-tx-widget', {
    template,

    props: {
        order: Object,
        componentType: {
            type: String,
            default: 'status'
        }
    },

    created () {
      this.initRequestConfig()
    },

    computed: {
        isEasyCreditPayment () {
            let tx = this.order.transactions;
            return tx.length == 1 && tx[0].paymentMethod.formattedHandlerIdentifier == easycreditFormattedHandlerIdentifier
        },
        transactionId() {
            return this.order.transactions[0].customFields.easycredit_transaction_id
        },
        transactionDate() {
            return this.order.transactions[0].createdAt
        }
    },

    methods: {
        initRequestConfig() {
            window.ratenkaufbyeasycreditOrderManagementConfig = {
                'endpoints': {
                    'get': 'api/v2/easycredit/transaction',
                    'list': 'api/v2/easycredit/transactions',
                    'post': 'api/v2/easycredit/transaction'
                },
                'request_config': {
                    'headers': {
                        'Content-Type': 'application/json',
                        'Authorization': 'Bearer ' + Context.api.authToken.access
                    }
                }
            }            
        }
    }
});