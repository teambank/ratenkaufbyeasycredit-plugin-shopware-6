import './extension/sw-settings-index';

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
