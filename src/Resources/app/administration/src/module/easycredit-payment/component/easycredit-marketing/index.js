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

        /*
        moveFields() {
            var widgetEnabledProductPages = document.querySelector('.sw-system-config--field-easy-credit-ratenkauf-config-widget-enabled-product-pages')
            document.querySelector('.easycredit-marketing__tab-content.widget .easycredit-marketing__content').append(widgetEnabledProductPages)

            var modalEnabled = document.querySelector('.sw-system-config--field-easy-credit-ratenkauf-config-modal-enabled')
            document.querySelector('.easycredit-marketing__tab-content.modal .easycredit-marketing__content').append(modalEnabled)

            var cardEnabled = document.querySelector('.sw-system-config--field-easy-credit-ratenkauf-config-card-enabled')
            document.querySelector('.easycredit-marketing__tab-content.card .easycredit-marketing__content').append(cardEnabled)

            var flashboxEnabled = document.querySelector('.sw-system-config--field-easy-credit-ratenkauf-config-flashbox-enabled')
            document.querySelector('.easycredit-marketing__tab-content.flashbox .easycredit-marketing__content').append(flashboxEnabled)

            var barEnabled = document.querySelector('.sw-system-config--field-easy-credit-ratenkauf-config-bar-enabled')
            document.querySelector('.easycredit-marketing__tab-content.bar .easycredit-marketing__content').append(barEnabled)
        },
        */

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

        /*
        setConfig(event) {
            var element = event.target
            var value = null

            if ( element.type == 'checkbox' ) {
                value = element.checked
            } else {
                value = element.value
            }

            this.config[element.name] = value

            console.log('Config changed, saving ...')
            this.saveConfig()
        },
        fetchConfig() {
            const salesChannelId = this.getCurrentSalesChannelId()

            this.isLoading = true;

            return this.systemConfigApiService.getValues('EasyCreditRatenkauf.config', salesChannelId)
                .then(values => {
                    this.config = values;

                    console.log('Config Data:')
                    console.log(values)
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },
        saveConfig() {
            const salesChannelId = this.getCurrentSalesChannelId()

            this.isLoading = true;

            return this.systemConfigApiService.saveValues(this.config, salesChannelId)
                .then(() => {
                    this.createNotificationSuccess({
                        message: 'Settings saved',
                        autoClose: true,
                    });

                    return Promise.resolve();
                }).then(() => {
                    this.isLoading = false;
                });
        },
        */

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
        /*
        checkNumberFieldInheritance(value) {
            if (typeof value !== 'string' && typeof value !== 'number') {
                return true;
            }

            return value.toString().length <= 0;
        },
        */
        checkBoolFieldInheritance(value) {
            return typeof value !== 'boolean';
        },
    },

    created () {
        // this.fetchConfig()
    },

    mounted () {
        this.selectInitialTab()
        // this.moveFields()
    }
});
