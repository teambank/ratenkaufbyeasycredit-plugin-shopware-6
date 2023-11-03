import template from './sw-flow-easycredit-refund-modal.html.twig';
const { Component } = Shopware;


Component.register('sw-flow-easycredit-refund-modal', {
    template,

    props: {
        sequence: {
            type: Object,
            required: true,
        },
    },

    methods: {

        onClose() {
            this.$emit('modal-close');
        },

        onAddAction() {
            const sequence = {
                ...this.sequence,
                config: {
                    ...this.config
                },
            };

            this.$emit('process-finish', sequence);
        },
    },
});