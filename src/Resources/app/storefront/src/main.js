import EasyCreditRatenkaufCheckout from './checkout/checkout';
import EasyCreditRatenkaufWidget from './widget/widget';
import './easycredit-checkout';

const PluginManager = window.PluginManager;
PluginManager.register('EasyCreditRatenkaufCheckout', EasyCreditRatenkaufCheckout, '.is-ctl-checkout.is-act-confirmpage');
PluginManager.register('EasyCreditRatenkaufWidget', EasyCreditRatenkaufWidget,'body');
