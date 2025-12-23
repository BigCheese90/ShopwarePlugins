import template from './pricetest-index.html.twig';
import './pricetest-index.scss';
const { Criteria } = Shopware.Data;

const { Component } = Shopware;

export default {
    template,
    inject: [
        "repositoryFactory"
    ],
    data() {
        return {
            products: "FZCJ",
            discounts: null,
            result: undefined,
            isLoading: true,


        }
    },
    computed: {
        discountRepository() {
        return this.repositoryFactory.create("producer_prices")
        },
        columns() {
            return this.createColumns();
        },
        tabItems() {
            return this.createTabs();
        }
    },
    created() {
        const criteria = new Criteria();
        criteria.addAssociation('manufacturer');
        criteria.setPage(1);
        criteria.setLimit(10);
        this.discountRepository.search(criteria, Shopware.Context.api)
            .then(result => {
                this.result = result
                console.log(this.result)
            })
        this.isLoading = false

    },
    methods: {
        createColumns() {
            const columns = [

                {
                    property: 'manufacturer.name',
                    label: 'Hersteller',
                    allowResize: true,
                    primary: true,
                },
                {
                    property: 'discount',
                    label: 'discount',
                    inlineEdit: 'number',
                },
                {
                    property: 'priceReference',
                    label: 'priceReference',
                    allowResize: true,
                },

                {
                    property: 'userName',
                    label: 'Benutzername',
                    allowResize: true,
                },
                {
                    property: 'comment',
                    label: 'Kommentar',
                    allowResize: true,
                    inlineEdit: "string",

                },
                {
                    property: 'createdAt',
                    label: 'createdAt',
                    allowResize: true,
                    useCustomSort: true,

                },

        ]
        return columns;
        },
        createTabs() {
            const items = [
                {
                    "label": "Item 1",
                    "name": "item1"
                },
                {
                    "label": "Item 2 very long",
                    "name": "item2"
                }
                ]
            return items
        }
    }


}
/*
Component.register('pricetest-index', {
    template,


});*/
