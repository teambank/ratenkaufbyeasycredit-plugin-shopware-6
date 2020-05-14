import Plugin from 'src/plugin-system/plugin.class';

export default class EasyCreditRatenkaufCheckout extends Plugin {
    init() {

        var form = $('#easycredit-payment-form');
        $('input[type=radio][name=paymentMethodId]').change(function() {
            if (form.closest('.payment-method').find($(this)).length > 0) {
                $('#easycredit-agreement').attr('required','required');
            } else {
                $('#easycredit-agreement').removeAttr('required');
            }
        });
        
        $('#easycredit-recalculate').click(function(){
            $('#confirmPaymentForm').submit();
            return false;
        });
    }
}
