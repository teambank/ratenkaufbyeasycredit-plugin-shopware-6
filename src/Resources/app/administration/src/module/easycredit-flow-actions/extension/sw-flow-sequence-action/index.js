import { CAPTURE_ACTION, REFUND_ACTION, GROUP } from '../../constant/easycredit-flow.constant';

const { Component } = Shopware;

Component.override('sw-flow-sequence-action', {
    computed: {
        actionDescription() {
            const actionDescriptionList = this.$super('actionDescription');

            return {
                ...actionDescriptionList,
                [CAPTURE_ACTION.HANDLE] : this.$tc('easycredit.flow-actions.capture'),
                [REFUND_ACTION.HANDLE] : this.$tc('easycredit.flow-actions.refund'),
            };
        },
    },

    methods: {
        getActionTitle(actionName) {
            if (actionName === CAPTURE_ACTION.HANDLE) {
                return {
                    value: actionName,
                    icon: 'default-symbol-euro',
                    label: this.$tc('easycredit.flow-actions.capture'),
                    group: GROUP,
                }
            }
            if (actionName === REFUND_ACTION.HANDLE) {
                return {
                    value: actionName,
                    icon: 'default-arrow-360-full',
                    label: this.$tc('easycredit.flow-actions.refund'),
                    group: GROUP,
                }
            }

            return this.$super('getActionTitle', actionName);
        },
    },
});
