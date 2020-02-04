import template from './easycredit.html.twig';
import './easycredit.scss';

const { Mixin } = Shopware;

Shopware.Component.register('easycredit', {
    template,

    mixins: [
        Mixin.getByName('notification')
    ],

    inject: ['EasyCreditRatenkaufApiCredentialsService'],

    data() {
        return {
            isLoading: false,
            isTesting: false,
            isSaveSuccessful: false,
            isTestSuccessful: false,
            webshopIdFilled: false,
            apiPasswordFilled: false,
            config: null,
            webshopIdErrorState: null,
            apiPasswordErrorState: null
        };
    },

    metaInfo() {
        return {
            title: this.$createTitle()
        };
    },

    watch: {
        config: {
            handler() {
                const defaultConfig = this.$refs.configComponent.allConfigs.null;
                const salesChannelId = this.$refs.configComponent.selectedSalesChannelId;

                if (salesChannelId === null) {
                    this.webshopIdFilled = !!this.config['EasyCreditRatenkauf.settings.webshopId'];
                    this.apiPasswordFilled = !!this.config['EasyCreditRatenkauf.settings.apiPassword'];
                } else {
                    this.webshopIdFilled = !!this.config['EasyCreditRatenkauf.settings.webshopId']
                        || !!defaultConfig['EasyCreditRatenkauf.settings.webshopId'];
                    this.apiPasswordFilled = !!this.config['EasyCreditRatenkauf.settings.apiPassword']
                        || !!defaultConfig['EasyCreditRatenkauf.settings.apiPassword'];
                }
            },
            deep: true
        }
    },
    computed: {
        testButtonDisabled() {
            return this.isLoading || !this.apiPasswordFilled || !this.webshopIdFilled || this.isTesting;
        }
    },

    methods: {
        onSave() {
            if (!this.webshopIdFilled || !this.apiPasswordFilled) {
                this.setErrorStates();
                return;
            }

            this.save();
        },

        save() {
            this.isLoading = true;

            this.$refs.configComponent.save().then((res) => {
                this.isLoading = false;
                this.isSaveSuccessful = true;

                if (res) {
                    this.config = res;
                }

            }).catch(() => {
                this.isLoading = false;
            });
        },


        onTest() {
            this.isTesting = true;
            const webshopId = this.config['EasyCreditRatenkauf.settings.webshopId'] ||
                this.$refs.configComponent.allConfigs.null['EasyCreditRatenkauf.settings.webshopId'];
            const apiPassword = this.config['EasyCreditRatenkauf.settings.apiPassword'] ||
                this.$refs.configComponent.allConfigs.null['EasyCreditRatenkauf.settings.apiPassword'];
            const sandbox = this.config['EasyCreditRatenkauf.settings.sandbox'] ||
                this.$refs.configComponent.allConfigs.null['EasyCreditRatenkauf.settings.sandbox'];

            this.EasyCreditRatenkaufApiCredentialsService.validateApiCredentials(
                webshopId,
                apiPassword
            ).then((response) => {
                const credentialsValid = response.credentialsValid;

                if (credentialsValid) {
                    this.isTesting = false;
                    this.isTestSuccessful = true;
                }
            }).catch((errorResponse) => {
                if (errorResponse.response.data && errorResponse.response.data.errors) {
                    let message = `${this.$tc('easycredit.settingForm.messageTestError')}<br><br><ul>`;
                    errorResponse.response.data.errors.forEach((error) => {
                        message = `${message}<li>${error.detail}</li>`;
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
        },

        setErrorStates() {
            if (!this.webshopIdFilled) {
                this.webshopIdErrorState = {
                    code: 1,
                    detail: this.$tc('easycredit.messageNotBlank')
                };
            } else {
                this.webshopIdErrorState = null;
            }

            if (!this.apiPasswordFilled) {
                this.apiPasswordErrorState = {
                    code: 1,
                    detail: this.$tc('easycredit.messageNotBlank')
                };
            } else {
                this.apiPasswordErrorState = null;
            }
        }
    }
});