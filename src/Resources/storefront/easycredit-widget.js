import fetchJsonp from 'fetch-jsonp';

export default class EasyCreditWidget {
    
    constructor(element, opts) {
        this.element = element;

        const defaults = this.getDefaults();
        this.opts = {... defaults, ... opts};

        this.validate();

        const uri  = this._getApiUri(this.opts);
        this.getMinimumInstallment(uri)
            .then(this.prepareWidgetData)
            .then(this.renderWidget)
            .catch((e) => console.error(e));
    }

    validate() {
        const opts = this.opts;

        if (isNaN(opts.amount) || opts.amount < 200 || opts.amount > 10000) {
            if (opts.debug) {
                throw new Error(opts.amount+' is not within allowed range');
            }
            return;
        }

        if (opts.webshopId == null
            || opts.webshopId == ''
        ) {
            throw new Error('webshopId must be set for easycredit widget');
        }
    }

    getMinimumInstallment(uri) {

        const options = {
            headers: new Headers({'content-type': 'application/json; charset=utf-8'}),
        };

        return fetchJsonp(uri,options)
            .then((response) => {
                console.log(this);
                return response.json()
            });
    }

    prepareWidgetData = (res) => {

        if (!res || res.wsMessages.messages.length > 0) {
            return Promise.reject();
        }

        const data = {
            number_of_installments:   res.anzahlRaten,
            amount:                   this._formatAmount(res.betragRate),
            currency_symbol:          this.opts.currencySymbol,
            suffix:                   this.opts.suffix,
            link_text:                this.opts.linkText,
        };
        data.installmentTemplate = this._template(this.opts.installmentTemplate, data);

        return Promise.resolve(data);
    }

    renderWidget = (data) =>  {

        var widget = this._template(this.opts.widgetTemplate,data);
        //this.element.insertAdjacentHTML('beforeend', widget);

        var widgetNode = document.createElement('div');
        widgetNode.innerHTML = widget;
        
        this.element.parentNode.insertBefore(widgetNode, this.element.nextSibling);

        this.addStyles();

        widgetNode.querySelector('a')
            .addEventListener('click', this.showModal);
    }

    addStyles = () => {
        var style = document.createElement('style');
        style.innerHTML = `
            .easycredit-widget {
                display: block;
                color: #000;
                font-size: 13px;
                padding: 10px;
                display: inline-block;
                background-color:#fff;
                background-image: url(https://static.easycredit.de/content/image/logo/ratenkauf_42_55.png);
                background-size: 55px 42px;
                background-repeat: no-repeat;
                padding-left: 60px;
                background-position-y: center;
                min-width: 200px;
            }
            .easycredit-widget .easycredit-rate, 
            .easycredit-widget .easycredit-suffix {
                font-weight: 700;
            }
            .easycredit-widget .easycredit-link {
                cursor: pointer;
            }
            `;

        var ref = document.querySelector('script');
        ref.parentNode.insertBefore(style, ref);
    }

    getDefaults() {
        return {
            hostname: 'https://ratenkauf.easycredit.de',
            endpoint: '/ratenkauf-ws/rest/v1/modellrechnung/guenstigsterRatenplan',
            iframeSrc: '/widget/app/#/ratenwunsch',
            modal: null, //easycreditModal.init.bind(easycreditModal),
            webshopId: null,
            amount: null,
            debug: false,
            currencySymbol: '&euro;', //"\u25B2",
            installmentTemplate: '%amount% %currency_symbol% / Monat',
            widgetTemplate: [
                '<div class="easycredit-widget">',
                '<span class="easycredit-suffix">%suffix% </span>',
                '<span class="easycredit-rate">%installmentTemplate%</span>',
                '<br />',
                '<a class="easycredit-link">%link_text%</a>',
                '</div>',
            ].join('\n'),
            suffix: 'Finanzieren ab',
            linkText: 'mehr Infos zum Ratenkauf',
        };
    }

    _getApiUri(opts) {
        return [
            opts.hostname+opts.endpoint,
            this.param({
                webshopId: opts.webshopId,
                finanzierungsbetrag: opts.amount,
            }),
        ].join('?');
    }

    param(params) {
        const p = new URLSearchParams;
        for( const [ key, value ] of Object.entries( params ) ) {
            p.set( key, String( value ) );
        }
        return p.toString();
    }

    _getIframeUri(opts){
        return [
            opts.hostname+opts.iframeSrc,
            this.param({
                'shopKennung': opts.webshopId,
                'bestellwert': opts.amount,
            }),
        ].join('?');
    }
    
    _formatAmount( amount ) {
        return Number(Math.round(amount+'e2')+'e-2').toFixed(2).replace('.',',');
    }
    _template( template, data ){
        return template
            .replace(
                /%(\w*)%/g,
                function ( m, key ){
                    return data.hasOwnProperty( key ) ? data[ key ] : '';
                }
            );
    }
 
    _getModalContent(uri) {
        return '<iframe class="easycredit-modal" src="' + uri + '"></iframe>';
    }
    
    showModal = () => {
        var content = this._getModalContent(
            this._getIframeUri(this.opts)
        );
        this.opts.modal(content);
    }
    
    /*rkPaymentPage = function(opts) {
        var opts = $.extend({}, defaults, opts);
        var me = $(this);
    
        if ($(this).data('easycredit-amount')) {
            opts.amount = $(this).data('easycredit-amount');
        }
    
        if (isNaN(opts.amount) || opts.amount < 200 || opts.amount > 10000) {
            if (opts.debug) {
                console.log(opts.amount+' is not within allowed range');
            }
            return;
        }
    
        if (opts.webshopId == null
            || opts.webshopId == ''
        ) {
            throw new Error('webshopId must be set for easycredit widget');
        }
    
        var uri = getApiUri(opts);
        getMinimumInstallment(uri, function(res){
            if (!res || res.wsMessages.messages.length > 0) {
                return;
            }
    
            var data = {
                number_of_installments:   res.anzahlRaten,
                amount:                   formatAmount(res.betragRate),
                currency_symbol:          opts.currencySymbol,
                suffix:                   opts.suffix,
                link_text:                opts.linkText
            };
            data.installmentTemplate =    template(opts.installmentTemplate, data);
    
            var widget = $(template(opts.widgetTemplate,data));
            $(me).append(widget);
            widget.find('a').click(
                _showModal.bind(this, me, opts)
            );
        });
    } */
}
/*
var easycreditModal = {
    template: [
        '<div class="easycredit-modal">',
        '<div class="easycredit-embed-responsive"></div>',
        '</div>'
    ].join('\n'),
    handleModal: function(element, content) {
        var modal = $(this.template)
        modal.find('.easycredit-embed-responsive').append(content).css({
            'height': '900px',
            'max-height':'900px'
        });
        $(element).append(modal);
        modal.easycreditmodal();
    },
    init: function(element, content) {
        this.handleModal(element,content);
    }
}

$.fn.rkPaymentPage = function(opts) {
    return this.each(function(index,element){
        rkPaymentPage.apply(element,[opts]);
    });
};

window.rkPlugin = {};
window.rkPlugin.anzeige = function(componentID, options) {
    $('#' + componentID).rkPaymentPage(options);
};
}));
*/
