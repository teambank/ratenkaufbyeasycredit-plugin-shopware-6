import { Component, Filter, Mixin, Context } from 'src/core/shopware';
const Criteria = Shopware.Data.Criteria;

import '../../../../app/component/easycredit-payment-actions';
import template from './easycredit-payment-detail.html.twig';
import './easycredit-payment-detail.scss';

Component.register('easycredit-payment-detail', {
    template,

    mixins: [
        Mixin.getByName('notification')
    ],

    inject: [
        'repositoryFactory'
    ],

    watch: {
        '$route'() {
            this.resetDataAttributes();
            this.createdComponent();
        }
    },

    data() {
        return {
            isLoading: true,
            order: null
        };
    },
    created() {
        this.createdComponent();
    },

    methods: {
        createdComponent() {
            const orderId = this.$route.params.id;
            const orderRepository = this.repositoryFactory.create('order');
            const orderCriteria = new Criteria(1, 1);
            orderCriteria.addAssociation('transactions.stateMachineState');
            orderCriteria.getAssociation('transactions').addSorting(Criteria.sort('createdAt'));

            orderRepository.get(orderId, Context.api, orderCriteria).then((order) => {
                this.order = order;
                this.isLoading = false;
            })
        }
    }
});
