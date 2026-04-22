

const {Module} = Shopware;



Shopware.Component.register('discount-tags-list', () => import('./page/discount-index'));
Shopware.Component.register('discount-manufacturer-create', () => import('./page/create-manufacturer-discount'));
Shopware.Component.register('discount-product-create', () => import('./page/create-product-discount'));

Module.register('admintest-discounts', {
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
        createManufacturerDiscount: {
            component: "discount-manufacturer-create",
            path: "manufacturer-discount",
            meta: {
                parentPath: "admintest.discounts.list"
            }
        },
        createProductDiscount: {
            component: "discount-product-create",
            path: "product-discount",
            meta: {
                parentPath: "admintest.discounts.list"
            }
        },

        create: {
            component: "discount-tags-create",
            path: "create",
            meta: {
                parentPath: "admintest.discounts.list"
            }
        },

        createProductDiscountWithTag: {
            component: "discount-tags-product-create",
            path: "add-product-discount/:tagId",
            meta: {
                parentPath: "admintest.discounts.list"
            }
        },
    },



    navigation: [{
        label: 'discounts',
        color: '#F88962',
        path: 'admintest.discounts.list',
        icon: 'regular-shopping-bag',
        parent: 'sw-catalogue',
        position: 41
    }],


})
