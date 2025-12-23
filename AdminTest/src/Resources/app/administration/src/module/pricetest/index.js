

const {Module} = Shopware;

Shopware.Component.register('pricetest-index', () => import('./page/pricetest-index'));
Shopware.Component.register('pricetest-create', () => import('./page/pricetest-create'));

Module.register('admintest-pricetest', {
    type: 'plugin',
    name: "pricetest.general.title",
    title: 'pricetest.general.title',
    description: 'pricetest.general.description',
    color: '#F88962',
    icon: 'default-avatar-multiple',

    routes: {
        list: {
            component: 'pricetest-index',
            path: 'index'
        },
        create: {
            component: "pricetest-create",
            path: "create",
            meta: {
                parentPath: "admintest.pricetest.list"
            }
        }
    },



    navigation: [{
        label: 'pricetest.general.title',
        color: '#F88962',
        path: 'admintest.pricetest.list',
        icon: 'regular-shopping-bag',
        parent: 'sw-catalogue',
        position: 40
    }],


})
