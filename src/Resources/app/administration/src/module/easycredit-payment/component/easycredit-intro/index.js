import template from './easycredit-intro.html.twig';
import './easycredit-intro.scss';

const { Component } = Shopware;

Component.register('easycredit-intro', {
    template,

    inject: ['EasyCreditRatenkaufApiCredentialsService'],
    data() {
        return {
            billPaymentActive: false
        };
    },

    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset')
        }
    },

    beforeMount () {
        this.fetchWebshopInfo().then((data) => {
            this.billPaymentActive = data.billPaymentActive;
            console.log('billPaymentActive:', data.billPaymentActive);
        });
    },
    methods: {
        getConfigComponent () {
            var component = this
            while (component.$parent) {
                if (typeof component.currentSalesChannelId !== 'undefined') {
                    return component
                }
                component = component.$parent
            }
        },
        getConfig(salesChannelId) {
            return this.getConfigComponent().actualConfigData[salesChannelId]
        },
        getCurrentSalesChannelId() {
            return this.getConfigComponent().currentSalesChannelId
        },
        fetchWebshopInfo () {
            const salesChannelId = this.getCurrentSalesChannelId();
            const webshopId = this.getConfig(salesChannelId)['EasyCreditRatenkauf.config.webshopId'] ||
                this.getConfig(null)['EasyCreditRatenkauf.settings.webshopId'];
            const url = `https://ratenkauf.easycredit.de/api/payment/v3/webshop/${webshopId}`;

            return fetch(url).then((response) => {
                if (!response.ok) { 
                    return Promise.reject(response); 
                }
                return response.json();
            }).then((data) => {
                // console.log('Fetched data:', data);
                return data;
            }).catch((error) => {
                console.error('Error fetching webshop info:', error);
            });
        }
    }
});
