import './components/easycredit-settings-icon';

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
    },

    settingsItem: {
        group: 'plugins',
        to: {
            name: 'sw.extension.config',
            params: {
                namespace: 'EasyCreditRatenkauf'
            }
        },
        iconComponent: 'easycredit-settings-icon',
        backgroundEnabled: true,
        privilege: 'system.system_config',
    }
});
