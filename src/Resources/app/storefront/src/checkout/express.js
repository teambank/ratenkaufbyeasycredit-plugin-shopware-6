import Plugin from 'src/plugin-system/plugin.class'
import { getCsrfToken, createHiddenField } from '../util.js'

export default class EasyCreditRatenkaufExpressCheckout extends Plugin {
    init() {
        this.el.addEventListener('submit', async () => {

            var form
            if (form = await this.replicateBuyForm()) {
                form.submit()
                return
            }

            if (
                document.querySelector('.is-ctl-checkout.is-act-cartpage') ||
                this.el.closest('.cart-offcanvas')
            ) {
                window.location.href = '/easycredit/express'
                return
            }

            alert('Der easycredit-Ratenkauf konnte nicht gestartet werden.')
        })
    }

    async replicateBuyForm () {
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

        let token = await getCsrfToken()
        if (token) {
          form.append(createHiddenField('_csrf_token', token))
        }

        document.querySelector('body').append(form)
        return form
    }
}
