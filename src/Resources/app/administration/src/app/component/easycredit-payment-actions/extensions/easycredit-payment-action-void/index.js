import { Component, Mixin } from 'src/core/shopware';
import utils from 'src/core/service/util.service';
import template from './easycredit-payment-action-void.html.twig';

Component.register('easycredit-payment-action-void', {
    template,

    inject: ['EasyCreditPaymentService'],

    mixins: [
        Mixin.getByName('notification')
    ],

    props: {
        paymentResource: {
            type: Object,
            required: true
        }
    },

    data() {
        return {
            isLoading: false
        };
    },

    methods: {
        voidPayment() {
            this.isLoading = true;
            const resourceType = this.paymentResource.intent;
            const resourceId = this.getResourceId();

            this.EasyCreditPaymentService.voidPayment(resourceType, resourceId).then(() => {
                this.createNotificationSuccess({
                    title: this.$tc('easycredit-payment.voidAction.successTitle'),
                    message: this.$tc('easycredit-payment.voidAction.successMessage')
                });
                this.isLoading = false;
                this.closeModal();
                this.$nextTick(() => {
                    this.$router.replace(`${this.$route.path}?hash=${utils.createId()}`);
                });
            }).catch((errorResponse) => {
                this.createNotificationError({
                    title: errorResponse.title,
                    message: errorResponse.message
                });
                this.isLoading = false;
            });
        },

        getResourceId() {
            const firstRelatedResource = this.paymentResource.transactions[0].related_resources[0];

            if (firstRelatedResource.order) {
                return firstRelatedResource.order.id;
            }

            return firstRelatedResource.authorization.id;
        },

        closeModal() {
            this.$emit('modal-close');
        }
    }
});
