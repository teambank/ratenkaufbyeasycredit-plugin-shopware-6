import template from './easycredit-credentials.html.twig';

const { Component } = Shopware;

Component.register('easycredit-credentials', {
    template,
    name: 'NetzkollektivEasycreditCredentials',

    props: {
        actualConfigData: {
            type: Object,
            required: true
        },
        allConfigs: {
            type: Object,
            required: true
        },
        selectedSalesChannelId: {
            required: true
        }
    },

    methods: {
        checkTextFieldInheritance(value) {
            if (typeof value !== 'string') {
                return true;
            }

            return value.length <= 0;
        },

        checkBoolFieldInheritance(value) {
            if (typeof value !== 'boolean') {
                return true;
            }

            return false;
        }
    }
});