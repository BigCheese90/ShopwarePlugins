import template from './pricetest-index.html.twig';
import './pricetest-index.scss';
import * as XLSX from "xlsx";

import {BButton, BTable} from "bootstrap-vue-next"


const { Criteria } = Shopware.Data;

export default {
    template,
    inject: [
        "repositoryFactory"
    ],
    components: { BButton, BTable },
    data() {
        return {

            discounts: null,
            productDiscounts: null,
            custom_price: null,
            result: undefined,
            isLoading: true,
            tagId: null,
            tagLabel: null,
            tagName: null,
            excelFile: null,
            uploadRows: null,
            activeTab: "manufacturerDiscounts",
            fileUploaded: false,




        }
    },
    computed: {

        discountRepository() {
            const repository = this.repositoryFactory.create("producer_prices")
            const criteria = new Criteria();
            criteria.addAssociation('manufacturer');
            criteria.addFilter(Criteria.equals("tagId", this.tagId))
            repository.search(criteria, Shopware.Context.api)
                .then(result => {
                    this.result = result
                })

        return repository
        },
        productDiscountRepository() {
            const repository = this.repositoryFactory.create("discount_products")
            const criteria = new Criteria();
            criteria.addAssociation('product');
            criteria.addFilter(Criteria.equals("tagId", this.tagId))
            repository.search(criteria, Shopware.Context.api)
                .then(result => {
                    this.productDiscounts = result
                })

            return repository
        },
        what() {
            console.log("lol");
        },

        columns() {
            console.log("what the fuck")
            return this.createColumns();
        },
        productColumns() {
            return this.createProductColumns();
        },
        tabItems() {
            return this.createTabs();
        }
    },
    created() {
        this.isLoading = false

    },
    watch: {
        tagId: {
            handler(newTagId) {
                this.loadTagName(newTagId);
            }
        }
    },
    methods: {
        productRepository() {
            const repository = this.repositoryFactory.create("product")
            const criteria = new Criteria();
            repository.search(criteria, Shopware.Context.api)
                .then(result => {
                    this.productDiscounts = result
                });

            return repository
        },
        loadTagName(tagId) {
                console.log("help");
                const repository = this.repositoryFactory.create("tag");
                if (tagId != null) {
                    console.log("tagId found")
                    const criteria = new Criteria();
                    criteria.addFilter(Criteria.equals("id", tagId))
                    repository.search(criteria, Shopware.Context.api)
                        .then(result => {
                            this.tagName = result.first().name; console.log(result.first().name)
                })}
                else {
                    console.log("no name")
                    this.tagName = null;
                }


            },
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
                    property: 'priceReference.name',
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
        createProductColumns() {
            const columns = [
                {
                    property: 'product.productNumber',
                    label: 'Artikelnummer',
                    allowResize: true,
                    primary: true,
                },
                {
                    property: 'product.manufacturerNumber',
                    label: 'Herstellernummer',
                    allowResize: true,
                    primary: true,
                },

                {
                    property: 'product.name',
                    label: 'Produkt',
                    allowResize: true,
                    width: 10,
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
                    property: 'fixedPrice',
                    label: 'fixedPrice',
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
                    "label": "Hersteller",
                    "name": "manufacturerDiscounts",

                },
                {
                    "label": "Produkte",
                    "name": "productDiscounts"
                }
                ]
            return items
        },
        handleItemActive(item) {
            console.log('Caught item from child:', item);
            this.activeTab = item
        },
        sets(){
            return ["Set1", "Set2"]
        }
        
    }


}

