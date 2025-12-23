

const {Module} = Shopware;



Shopware.Component.register('discount-tags-list', () => import('./page/pricetest-index'));
Shopware.Component.register('discount-tags-create', () => import('./page/pricetest-create'));
Shopware.Component.register('discount-tags-product-create', () => import('./page/create-product-discount'));

Module.register('admintest-discount-tags', {
    type: 'plugin',
    name: "Teest",
    title: 'Teest',
    description: 'pricetest.general.description',
    color: '#F88962',
    icon: 'default-avatar-multiple',

    routes: {
        list: {
            component: 'discount-tags-list',
            path: 'index'
        },
        createTag: {
            component: "discount-tags-create",
            path: "create/:tagId",
            meta: {
                parentPath: "admintest.discount.tags.list"
            }
        },
        create: {
            component: "discount-tags-create",
            path: "create",
            meta: {
                parentPath: "admintest.discount.tags.list"
            }
        },
        createProductDiscount: {
            component: "discount-tags-product-create",
            path: "add-product-discount",
            meta: {
                parentPath: "admintest.discount.tags.list"
            }
        },
        createProductDiscountWithTag: {
            component: "discount-tags-product-create",
            path: "add-product-discount/:tagId",
            meta: {
                parentPath: "admintest.discount.tags.list"
            }
        },
    },



    navigation: [{
        label: 'discount-tags',
        color: '#F88962',
        path: 'admintest.discount.tags.list',
        icon: 'regular-shopping-bag',
        parent: 'sw-catalogue',
        position: 41
    }],


})
