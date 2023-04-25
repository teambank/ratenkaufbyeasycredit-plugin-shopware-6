/* 
this file was renamed during migration to sw 6.5 
it should not be picked up by Shopware webpack building for cross-compatibility (webpack 4 / 5) 
*/
import PluginManagerClass from 'src/plugin-system/pluginmanager.class'

import EasyCreditRatenkaufCheckout from './checkout/checkout';
import EasyCreditRatenkaufCheckoutExpress from './checkout/express';
import EasyCreditRatenkaufWidget from './widget/widget';
import EasyCreditRatenkaufMarketing from './marketing/marketing';

const PluginManager = new PluginManagerClass()
// const PluginManager = window.PluginManager; // migration to v6.5, cross-compatibility: we use our own "PluginManager"
PluginManager.register('EasyCreditRatenkaufCheckout', EasyCreditRatenkaufCheckout, '.is-ctl-checkout.is-act-confirmpage');
PluginManager.register('EasyCreditRatenkaufCheckoutExpress', EasyCreditRatenkaufCheckoutExpress, 'easycredit-express-button');
PluginManager.register('EasyCreditRatenkaufWidget', EasyCreditRatenkaufWidget, 'body');
PluginManager.register('EasyCreditRatenkaufMarketing', EasyCreditRatenkaufMarketing, 'body');
