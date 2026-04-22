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
            customerId: null,
            tagId: null,
            tagLabel: null,
            tagName: null,
            excelFile: null,
            uploadRows: null,
            activeTab: "manufacturerDiscounts",
            discountCategoryTab: "allCustomers",
            fileUploaded: false,
            allManufacturerDiscounts: [],
            tagManufacturerDiscounts: [],
            customerManufacturerDiscounts: [],
            allProductDiscounts: [],
            tagProductDiscounts: [],
            customerProductDiscounts: [],

        }
    },
    computed: {
        customerCriteria() {
            const criteria = new Criteria();
            criteria.addAssociation('defaultBillingAddress')
            return criteria
        },
        customerLabelCallback() {
            return "abcd"
        },
        manufacturerdiscountRepository() {
            return this.repositoryFactory.create("discount_manufacturer")
        },
        productDiscountRepository() {
            return this.repositoryFactory.create("discount_product")
        },
        // allProductDiscountRepository() {
        //     const repository = this.repositoryFactory.create("discount_products")
        //     const criteria = new Criteria();
        //     criteria.addAssociation('product');
        //     criteria.addFilter(Criteria.multi('AND',
        //         [Criteria.equals("tagId", null),
        //     Criteria.equals("customerId", null)]));
        //     repository.search(criteria, Shopware.Context.api)
        //         .then(result => {
        //             this.productDiscounts = result
        //         })
        //
        //     return repository
        // },
        // singleProductDiscountRepository() {
        //     const repository = this.repositoryFactory.create("discount_products")
        //     const criteria = new Criteria();
        //     criteria.addAssociation('product');
        //     criteria.addFilter(Criteria.equals("customerId", this.customerId));
        //     criteria.addFilter(Criteria.not("AND", [Criteria.equals("customerId", null)]));
        //     repository.search(criteria, Shopware.Context.api)
        //         .then(result => {
        //             this.productDiscounts = result
        //         })
        //
        //     return repository
        // },
        // tagProductDiscountRepository() {
        //     const repository = this.repositoryFactory.create("discount_products")
        //     const criteria = new Criteria();
        //     criteria.addAssociation('product');
        //     criteria.addFilter(Criteria.equals("tagId", this.tagId))
        //     repository.search(criteria, Shopware.Context.api)
        //         .then(result => {
        //             this.productDiscounts = result
        //         })
        //
        //     return repository
        // },
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
        categoryTabs() {
            return this.discountCategoryTabs();
        },
        tabItems() {
            return this.createTabs();
        }
    },
    created() {
        this.isLoading = false;
        this.getAllManufacturerDiscounts();
        this.getAllProductDiscounts();

    },
    watch: {
        tagId: {
            handler(newTagId) {
                this.loadTagName(newTagId);
                this.getTagManufacturerDiscounts();
                this.getTagProductDiscounts();
            }
        },
        customerId: {
            handler() {
                this.getcustomerManufacturerDiscounts();
                this.getCustomerProductDiscounts();
            }
        }
    },
    methods: {
        getAllManufacturerDiscounts() {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals("customerId", null));
            criteria.addFilter(Criteria.equals("tagId", null));
            criteria.addAssociation('manufacturer')
            criteria.addAssociation('priceReference')
            this.manufacturerdiscountRepository.search( criteria, Shopware.Context.api).then( result => {
                this.allManufacturerDiscounts = result
            })
        },
        getTagManufacturerDiscounts() {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals("customerId", null));
            criteria.addFilter(Criteria.equals("tagId",this.tagId));
            criteria.addFilter(Criteria.not("AND", [Criteria.equals("tagId", null)]));
            criteria.addAssociation('manufacturer')
            criteria.addAssociation('priceReference')
            this.manufacturerdiscountRepository.search( criteria, Shopware.Context.api).then( result => {
                this.tagManufacturerDiscounts = result
            })
        },
        getcustomerManufacturerDiscounts() {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals("tagId", null));
            criteria.addFilter(Criteria.equals("customerId",this.customerId));
            criteria.addFilter(Criteria.not("AND", [Criteria.equals("customerId", null)]));
            criteria.addAssociation('manufacturer')
            criteria.addAssociation('priceReference')
            this.manufacturerdiscountRepository.search( criteria, Shopware.Context.api).then( result => {
                this.customerManufacturerDiscounts = result
            })
        },
        getAllProductDiscounts() {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals("customerId", null));
            criteria.addFilter(Criteria.equals("tagId", null));
            criteria.addAssociation('product')
            this.productDiscountRepository.search( criteria, Shopware.Context.api).then( result => {
                this.allProductDiscounts = result
            })
        },
        getTagProductDiscounts() {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals("customerId", null));
            criteria.addFilter(Criteria.equals("tagId", this.tagId));
            criteria.addFilter(Criteria.not("AND", [Criteria.equals("tagId", null)]));
            criteria.addAssociation('product')
            this.productDiscountRepository.search( criteria, Shopware.Context.api).then( result => {
                this.tagProductDiscounts = result
            })
        },
        getCustomerProductDiscounts() {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals("tagId", null));
            criteria.addFilter(Criteria.equals("customerId", this.customerId));
            criteria.addFilter(Criteria.not("AND", [Criteria.equals("customerId", null)]));
            criteria.addAssociation('product')
            this.productDiscountRepository.search( criteria, Shopware.Context.api).then( result => {
                this.customerProductDiscounts = result
            })
        },
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
                    numberType: 'float',
                    config: {
                        step: 0.001, // Defines the increment and allowed precision
                        min: 0,
                        digits: 3    // Explicitly tells the component to allow 4 decimal places
                    }
                },
                {
                    property: 'priceReference.config.label.en-GB',
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
                    property: 'priceReference.config.label',
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

        discountCategoryTabs() {
            const tabs = [
                {
                    "label": "Alle Kunden",
                    "name": "allCustomers"
                },
                {
                    "label": "Discount-Gruppen",
                    "name": "tags"
                },
                {
                    "label": "Einzelne Kunden",
                    "name": "singleCustomer"
                }
            ]
            return tabs
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
        discountCategoryActive(item) {
            console.log('Caught item from child:', item);
            this.discountCategoryTab = item
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

