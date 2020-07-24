import Plugin from 'src/plugin-system/plugin.class';
import EasyCreditWidget from '../easycredit-widget.js';
import PseudoModalUtil from 'src/utility/modal-extension/pseudo-modal.util';

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

        new EasyCreditWidget(this.el,{
            webshopId: this.getMeta('api-key'),
            amount: amount,
            modal: this.createModal
        });        
    }

    getMeta(key) {
        const meta = document.querySelector('meta[name=easycredit-'+key+']');
        if (meta === null) {
            return null;
        }
        return meta.content;
    }

    createModal(content) {
        const modal = new PseudoModalUtil(content);
        modal.open();
        const modalElement = modal.getModal();
        modalElement.querySelector('.modal-dialog').classList.add('modal-lg');
    }
}