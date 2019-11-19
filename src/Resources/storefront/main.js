import NetzkollektivEasyCreditCheckout from './checkout/checkout';
import NetzkollektivEasyCreditWidget from './widget/widget';

const PluginManager = window.PluginManager;
PluginManager.register('NetzkollektivEasyCreditCheckout', NetzkollektivEasyCreditCheckout, '.is-ctl-checkout.is-act-confirmpage');
PluginManager.register('NetzkollektivEasyCreditWidget', NetzkollektivEasyCreditWidget,'body');