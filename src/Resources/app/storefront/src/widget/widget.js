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

        let amount = this.getMeta('amount');
        if (null === amount || isNaN(amount)) {
            const priceContainer = this.el.parentNode;
            amount = priceContainer.querySelector('[itemprop=price]') ? 
                priceContainer.querySelector('[itemprop=price]').content 
                : null;
        }
        
        if (null === amount || isNaN(amount)) {
            return;
        }
        let widget = document.createElement('easycredit-widget')
        widget.setAttribute('webshop-id', this.getMeta('api-key'))
        widget.setAttribute('amount', this.getMeta('amount'))
        if ( this.getMeta('widget-extended') ) {
            widget.setAttribute('extended', this.getMeta('widget-extended'))
        }
        if ( this.getMeta('widget-display-type') ) {
            widget.setAttribute('display-type', this.getMeta('widget-display-type'))
        }

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
