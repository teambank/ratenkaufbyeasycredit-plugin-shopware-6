import EasyCreditRatenkaufCheckout from './checkout/checkout';
import EasyCreditRatenkaufWidget from './widget/widget';
import EasyCreditRatenkaufMarketing from './marketing/marketing';

const PluginManager = window.PluginManager;
PluginManager.register('EasyCreditRatenkaufCheckout', EasyCreditRatenkaufCheckout, '.is-ctl-checkout.is-act-confirmpage');
PluginManager.register('EasyCreditRatenkaufWidget', EasyCreditRatenkaufWidget, 'body');
PluginManager.register('EasyCreditRatenkaufMarketing', EasyCreditRatenkaufMarketing, 'body');
