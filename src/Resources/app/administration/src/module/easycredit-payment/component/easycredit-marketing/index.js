import template from './easycredit-marketing.html.twig';
import './easycredit-marketing.scss';

const { Component, Context } = Shopware;

Component.register('easycredit-marketing', {
    template,

    inject: [
        'systemConfigApiService',
    ],

    mixins: [
        'notification',
    ],

    data() {
        return {
            selectedTab: null,
            tabs: [
                { id: 'intro', title: this.$tc('easycredit-payment.marketing.overview.title') },
                { id: 'widget', title: this.$tc('easycredit-payment.marketing.widget.title') },
                { id: 'modal', title: this.$tc('easycredit-payment.marketing.modal.title') },
                { id: 'card', title: this.$tc('easycredit-payment.marketing.card.title') },
                { id: 'flashbox', title: this.$tc('easycredit-payment.marketing.flashbox.title') },
                { id: 'bar', title: this.$tc('easycredit-payment.marketing.bar.title') },
            ],
            isLoading: false,
            config: {}
        };
    },

    methods: {
        selectTab(index) {
            this.selectedTab = index
        },
        selectInitialTab() {
            this.selectedTab = this.tabs[0].id
        },

        getConfigComponent() {
            var component = this
            while (component.$parent) {
                if (typeof component.currentSalesChannelId !== 'undefined') {
                    return component
                }
                component = component.$parent
            }
        },
        getCurrentSalesChannelId() {
            return this.getConfigComponent().currentSalesChannelId
        },
        getConfig(salesChannelId) {
            return this.getConfigComponent().actualConfigData[salesChannelId]
        },

        textToNumber(event) {
            var element = event.target
            var oldValue = element.value

            if ( oldValue ) {
                element.value = oldValue.replace(/[^\d]/,'')
            }
        },

        checkTextFieldInheritance(value) {
            if (typeof value !== 'string') {
                return true;
            }

            return value.length <= 0;
        },
        checkBoolFieldInheritance(value) {
            return typeof value !== 'boolean';
        },
    },

    mounted () {
        this.selectInitialTab()
    }
});
