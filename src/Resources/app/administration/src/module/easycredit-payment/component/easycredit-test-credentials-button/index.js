import template from './easycredit-test-credentials-button.html.twig';
import './easycredit.scss';

const { Mixin } = Shopware;

Shopware.Component.register('easycredit-test-credentials-button', {
    template,

    mixins: [
        Mixin.getByName('notification')
    ],

    inject: ['EasyCreditRatenkaufApiCredentialsService'],
    data() {
        return {
            isLoading: false,
            isTesting: false,
            isTestSuccessful: false,
            testButtonDisabled: false
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
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
        onTest() {
            this.isTesting = true;

            const salesChannelId = this.getCurrentSalesChannelId();
            const webshopId = this.getConfig(salesChannelId)['EasyCreditRatenkauf.config.webshopId'] ||
                this.getConfig(null)['EasyCreditRatenkauf.settings.webshopId'];
            const apiPassword = this.getConfig(salesChannelId)['EasyCreditRatenkauf.config.apiPassword'] ||
                this.getConfig(null)['EasyCreditRatenkauf.settings.apiPassword'];
            const apiSignature = this.getConfig(salesChannelId)['EasyCreditRatenkauf.config.apiSignature'] ||
                this.getConfig(null)['EasyCreditRatenkauf.settings.apiSignature'];

            this.EasyCreditRatenkaufApiCredentialsService.validateApiCredentials(
                webshopId,
                apiPassword,
                apiSignature
            ).then((response) => {
                const credentialsValid = response.credentialsValid;

                if (credentialsValid) {
                    this.isTesting = false;
                    this.isTestSuccessful = true;
                }
            }).catch((errorResponse) => {
                if (errorResponse.response.data && errorResponse.response.data.errors) {
                    let message = `${this.$tc('easycredit.settingForm.messageTestError')}<br><ul>`;
                    errorResponse.response.data.errors.forEach((error) => {
                        message = `${message}<li><strong>${error.detail}</strong></li>`;
                    });
                    message += '</li>';
                    this.createNotificationError({
                        title: this.$tc('easycredit.settingForm.titleSaveError'),
                        message: message
                    });
                    this.isTesting = false;
                    this.isTestSuccessful = false;
                }
            });
        }
    }
});