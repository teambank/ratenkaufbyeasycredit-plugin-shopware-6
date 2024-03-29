import Plugin from 'src/plugin-system/plugin.class';

export default class EasyCreditRatenkaufWidget extends Plugin {
    init() {
        this.initWidget();
    }

    initWidget() {

        const selector = this.getMeta('widget-selector');
        if (selector === null
            || this.getMeta('api-key') === null
        ) {
            return;
        }

        this.el = document.querySelector(selector);
        if (!this.el) {
            return;
        }
 
        let amount = this.getMeta('amount');
        if (null === amount || isNaN(amount)) {
            const priceContainer = this.el.parentNode;
            amount = priceContainer && priceContainer.querySelector('[itemprop=price]') ? 
                priceContainer.querySelector('[itemprop=price]').content 
                : null;
        }
        
        if (null === amount || isNaN(amount)) {
            return;
        }
        let widget = document.createElement('easycredit-widget')
        widget.setAttribute('webshop-id', this.getMeta('api-key'))
        widget.setAttribute('amount', this.getMeta('amount'))

        this.el.appendChild(widget)
    }

    getMeta(key) {
        const meta = document.querySelector('meta[name=easycredit-'+key+']');
        if (meta === null) {
            return null;
        }
        return meta.content;
    }
}
