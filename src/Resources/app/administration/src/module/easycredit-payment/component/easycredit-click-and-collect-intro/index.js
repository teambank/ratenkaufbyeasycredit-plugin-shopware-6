import template from './easycredit-click-and-collect-intro.html.twig';
import './easycredit-click-and-collect-intro.scss';

const { Component } = Shopware;

Component.register('easycredit-click-and-collect-intro', {
    computed: {
        assetFilter() {
            return Shopware.Filter.getByName('asset')
        }
    },
    template
});