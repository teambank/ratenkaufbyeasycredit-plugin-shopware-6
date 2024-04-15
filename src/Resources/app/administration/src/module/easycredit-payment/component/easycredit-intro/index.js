import template from './easycredit-intro.html.twig';
import './easycredit-intro.scss';

const { Component } = Shopware;

Component.register('easycredit-intro', {
    template,

    data() {
        return {
        };
    },

    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset')
        }
    },

    methods: {
    },
});
