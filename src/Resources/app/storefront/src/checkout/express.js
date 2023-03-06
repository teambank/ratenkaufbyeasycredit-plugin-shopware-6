import Plugin from 'src/plugin-system/plugin.class'

export default class EasyCreditRatenkaufExpressCheckout extends Plugin {
    init() {
        this.el.addEventListener('submit', () => {

            var form
            if (form = this.replicateBuyForm()) {
                form.submit()
                return
            }

            if (document.querySelector('.is-ctl-checkout.is-act-cartpage')) {
                window.location.href = '/easycredit/express'
                return
            }

            alert('Der easycredit-Ratenkauf konnte nicht gestartet werden.')
        })
    }

    replicateBuyForm () {
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
}
