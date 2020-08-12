import './page/easycredit';
import './extension/sw-settings-index';
import './extension/sw-plugin';
import './components/easycredit-credentials';
import './components/easycredit-widget';

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

    routes: {
        index: {
            component: 'easycredit',
            path: 'index',
            meta: {
                parentPath: 'sw.settings.index'
            }
        }
    }
});
