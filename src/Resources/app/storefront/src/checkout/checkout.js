import Plugin from 'src/plugin-system/plugin.class';

export default class EasyCreditRatenkaufCheckout extends Plugin {
    init() {

        $('input[type=radio][name=paymentMethodId]').change(function() {
            if ($('.easycredit-payment-form').closest('.payment-method').find($(this)).length > 0) {
                $('.easycredit-payment-form').show();
                $('#easycredit-agreement').attr('required','required');
            } else {
                $('.easycredit-payment-form').hide();
                $('#easycredit-agreement').removeAttr('required');
            }
        });
        
        $('#easycredit-recalculate').click(function(){
            $('#confirmPaymentForm').submit();
            return false;
        });
    }
}
