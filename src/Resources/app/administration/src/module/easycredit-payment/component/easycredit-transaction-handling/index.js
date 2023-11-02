import template from './easycredit-transaction-handling.html.twig';

const { Component } = Shopware;

Component.register('easycredit-transaction-handling', {
    template,

    data() {
        return {
            flowBuilderAvailable: true
        }
    },
    created () {
        this.createdComponent()
    },
    methods: {
        createdComponent() {
            const hasFlowBuilder = (
                Shopware.Context.app.config.version.localeCompare('6.4.6.0', undefined, { numeric: true, sensitivity: 'base' }) > 0
            )
            if (!hasFlowBuilder) {
                this.flowBuilderAvailable = false
            }
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
        checkBoolFieldInheritance(value) {
            return typeof value !== 'boolean'
        }
    },
});
