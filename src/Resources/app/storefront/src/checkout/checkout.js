import Plugin from 'src/plugin-system/plugin.class';

export default class EasyCreditRatenkaufCheckout extends Plugin {
    init() {

        var handleEasyCreditRequired = function() {
            var form = $('#easycredit-payment-form');
            if (form.closest('.payment-method').find($(this)).length > 0) {
                $('#easycredit-agreement').attr('required','required');
            } else {
                $('#easycredit-agreement').removeAttr('required');
            }
        }

        $('input[type=radio][name=paymentMethodId]:checked').each(handleEasyCreditRequired);
        $('input[type=radio][name=paymentMethodId]').change(handleEasyCreditRequired);

        // < 6.4
        $('#easycredit-recalculate').click(function(){
            $('#confirmPaymentForm').submit();
            return false;
        });

        // >= 6.4
        $('easycredit-components-checkout').submit(function(){
            $('#changePaymentForm')
                .append('<input type="hidden" name="easycredit-submit" value="1" />')
                .submit();
            return false;
        });
    }
}
