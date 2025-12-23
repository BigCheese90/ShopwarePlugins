

import template from './template.twig';


Shopware.Component.override('sw-dashboard-index', {
    template
});
Shopware.Module.register('jakob-example', {
    type: 'plugin',
    name: 'Example',
    title: 'swag-example.general.mainMenuItemGeneral',
    description: 'sw-property.general.descriptionTextModule',
    color: '#ff3d58',
    icon: 'regular-shopping-bag',

    settingsItem: [{
        group: 'plugins',
        icon: 'regular-rocket',
        to: 'swag.plugin.list',
        name: 'SwagExampleMenuItemGeneral', // optional, fallback is taken from module
    }],

/*    routes: {
        list: {
            component: 'swag-example-list',
            path: 'list'
        },
        detail: {
            component: 'swag-example-detail',
            path: 'detail/:id',
            meta: {
                parentPath: 'swag.example.list'
            }
        },
        create: {
            component: 'swag-example-create',
            path: 'create',
            meta: {
                parentPath: 'swag.example.list'
            }
        }
    },*/

    navigation: [{
        id: 'swag-custommodule-list',
        label: 'CustomModule',
        color: '#ff3d58',
        path: 'sw.product.index',
        icon: 'default-shopping-paper-bag-product',
        parent: 'sw-catalogue',
        position: 100
    }]
});