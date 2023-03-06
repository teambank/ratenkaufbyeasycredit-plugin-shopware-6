import Plugin from 'src/plugin-system/plugin.class'

export default class EasyCreditRatenkaufCheckout extends Plugin {
    init() {

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
    }
}