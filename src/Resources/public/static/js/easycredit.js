
window.addEventListener('DOMContentLoaded', (event) => {
    // PluginManager.register('EasyCreditRatenkaufCheckout', EasyCreditRatenkaufCheckout, '.is-ctl-checkout.is-act-confirmpage');

    (function() {

        function createHiddenField(name, value) {
            var el = document.createElement('input')
            el.setAttribute('type','hidden')
            el.setAttribute('name',`easycredit[${name}]`)
            el.setAttribute('value',value)
            return el
        }

        document.querySelector('easycredit-checkout')?.addEventListener('submit', (e) => {
            var form = document.getElementById('changePaymentForm')
            form.append(createHiddenField('submit','1'))
            form.append(createHiddenField('number-of-installments', e.detail.numberOfInstallments))
            form.append(createHiddenField('agreement-checked', e.detail.privacyCheckboxChecked))
            form.submit() 

            return false
        })

    })();

    // PluginManager.register('EasyCreditRatenkaufWidget', EasyCreditRatenkaufWidget, 'body');

    (function() {

        var getMeta = function (key) {
            const meta = document.querySelector('meta[name=easycredit-'+key+']');
            if (meta === null) {
                return null;
            }
            return meta.content;
        }

        const selector = getMeta('widget-selector');
        if (selector === null
            || getMeta('api-key') === null
        ) {
            return;
        }

        var el = document.querySelector(selector);

        let amount = getMeta('amount');
        if (null === amount || isNaN(amount)) {
            const priceContainer = el.parentNode;
            amount = priceContainer.querySelector('[itemprop=price]') ? 
                priceContainer.querySelector('[itemprop=price]').content 
                : null;
        }
        
        if (null === amount || isNaN(amount)) {
            return;
        }
        
        let widget = document.createElement('easycredit-widget')
        widget.setAttribute('webshop-id', getMeta('api-key'))
        widget.setAttribute('amount', getMeta('amount'))

        el.appendChild(widget)

    })();


    // PluginManager.register('EasyCreditRatenkaufMarketing', EasyCreditRatenkaufMarketing, 'body');

    (function() {

        var body = document.querySelector('body');

        var bar = document.querySelector('easycredit-box-top');
        if ( bar ) {
            body.classList.add('easycredit-box-top');
        }

        var card = document.querySelector('.easycredit-box-listing');
        if ( card ) {
            var siblings = n => [...n.parentElement.children].filter(c=>c!=n);
            var siblingsCard = siblings(card);

            var position = card.querySelector('easycredit-box-listing').getAttribute('position');
            var previousPosition = ( typeof position === undefined ) ? null : Number( position - 1 );
            var appendAfterPosition = ( typeof position === undefined ) ? null : Number( position - 2 );

            if ( !position || previousPosition <= 0 ) {
                // do nothing
            } else if ( appendAfterPosition in siblingsCard ) {
                siblingsCard[appendAfterPosition].after(card);
            } else {
                card.parentElement.append(card);
            }
        }

    })();


    // PluginManager.register('EasyCreditRatenkaufExpressCheckout', EasyCreditRatenkaufExpressCheckout, 'easycredit-express-button');

    (function() {

        var replicateBuyForm = function () {
            let buyForm = document.getElementById('productDetailPageBuyProductForm')
            if (!buyForm) {
                return false
            }
            var form = document.createElement('form')
            form.setAttribute('action', buyForm.getAttribute('action'))
            form.setAttribute('method','post')
            form.style.display = 'none'
            
            var formData = new FormData(buyForm)
            formData.set('redirectTo', 'frontend.easycredit.express')
            formData.set('easycredit-express', '1')

            for (var key of formData.keys()) {
                let field = document.createElement('input')
                field.setAttribute('name', key)
                field.setAttribute('value', formData.get(key))
                form.append(field)
            }

            document.querySelector('body').append(form)
            return form
        }

        document.querySelector('easycredit-express-button').addEventListener('submit', () => {

            var form
            if (form = replicateBuyForm()) {
                form.submit()
                return
            }

            if (document.querySelector('.is-ctl-checkout.is-act-cartpage')) {
                window.location.href = '/easycredit/express'
                return
            }

            alert('Der easycredit-Ratenkauf konnte nicht gestartet werden.')
        })

    })();
});