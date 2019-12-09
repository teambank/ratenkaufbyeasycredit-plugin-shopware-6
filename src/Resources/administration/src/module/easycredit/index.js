import easycreditSettings from './page/easycredit';
import './extension/sw-settings-index';
import './components/easycredit-credentials';
import './components/easycredit-widget';
//import './components/easycredit-behavior';


import deDE from './snippet/de-DE.json';
import enGB from './snippet/en-GB.json';

const { Module } = Shopware;

Module.register('netzkollektiv-easycredit', {
    type: 'plugin',
    name: 'EasyCreditRatenkauf',
    title: 'easycredit.general.mainMenuItemGeneral',
    description: 'easycredit.general.descriptionTextModule',
    version: '1.0.0',
    targetVersion: '1.0.0',
    color: '#9AA8B5',
    icon: 'default-action-settings',

    snippets: {
        'de-DE': deDE,
        'en-GB': enGB
    },

    routes: {
        index: {
            component: easycreditSettings,
            path: 'index',
            meta: {
                parentPath: 'sw.settings.index'
            }
        }
    }
});
