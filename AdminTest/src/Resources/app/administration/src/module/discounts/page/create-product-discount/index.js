import template from './product-discount.html.twig';
import {ref} from "vue";
import * as XLSX from "xlsx";
import {BButton, BTable} from "bootstrap-vue-next";

const { Criteria } = Shopware.Data;

const { Component } = Shopware;
console.log(Shopware)
export default {
    template,
    inject: [
        "userService",
        "repositoryFactory"
    ],
    components: { BButton, BTable },
    data() {
        return {
            discountTarget: null,
            tagId: null,
            customerId: null,
            excelFile: "null",
            fileUploaded: false,
            uploadRows: null,
            discounts: null,
            result: undefined,
            discount: null,
            isLoading: false,
            tagName: "Alle Kunden",
            fixedPrice: ref(true),
            tagIdForUpload: null,

        }
    },
    computed: {
        productDiscountRepository() {
        return this.repositoryFactory.create("discount_product")
        },
        customerCriteria() {
            const criteria = new Criteria();
            criteria.addAssociation('defaultBillingAddress')
            return criteria
        },
        customFieldCriteria() {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.contains("name", "custom_product_pricing"));
            return criteria;
        },
        tagId() {
            return this.$route.params.tagId
        }
    },

    mounted() {
        console.log("ID from route:", this.tagId);
    },
    created() {
        this.discount = this.productDiscountRepository.create();
        this.getUser().then(user =>
            this.discount.userName = user);
        this.user = this.userService.getUser();
        this.discount.discount = 1;

        if (this.tagId) {
            this.discount.tagId = this.tagId;
            const tagRepository = this.repositoryFactory.create("tag")
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals("id", this.tagId))
            tagRepository.search(criteria, Shopware.Context.api)
                .then(result => {
                    this.tagName = result.first().name;
                    this.tagIdForUpload = this.tagId;
                    console.log(result.first().name)
                })
        }
    },
    methods: {
        customerLabelProperty() {
            return ["customerNumber", "defaultBillingAddress.company", ]
        },
        discountTargetList() {
            return [
                {
                    "label": "Alle Kunden",
                    "value": "allCustomers"
                },
                {
                    "label": "Tag",
                    "value": "tag",
                },
                {
                    "label": "Kunde",
                    "value": "customer",
                }
            ]
        },
        async uploadSingleDiscount(row) {
            console.log("row starting:", row["article_number"]);
            if (row["productId"] === "Not Found") {
                console.log("Product not found")
                return
            }
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals("tagId", this.tagIdForUpload))
            criteria.addFilter(Criteria.equals("productId", row["productId"]))

            await this.productDiscountRepository.search(criteria, Shopware.Context.api)
                .then(result => {if (result.total===0) {
                    console.log("Not Found");
                    return this.productDiscountRepository.create(Shopware.Context.api);
                } else {
                    console.log("first ID", result.first().id)
                    return this.productDiscountRepository.get(result.first().id, Shopware.Context.api)
                }
                }).then(discountEntity => {
                    console.log(row);
                    discountEntity.productId = row["productId"];
                    discountEntity.discount = row["discount"];
                    discountEntity.fixedPrice = row["fixed_price"];
                    discountEntity.tagId = this.tagIdForUpload;
                    console.log(row["discount"]);
                    console.log("fixedPriceis", discountEntity.fixedPrice);
                    /* discountEntity.fixedPrice = Number(discountEntity.fixedPrice.replace(",","."));*/
                    console.log(discountEntity.fixedPrice);

                    this.productDiscountRepository.save(discountEntity, Shopware.Context.api);
                })
        },
        uploadAllDiscounts() {
            /*this.uploadSingleDiscount(this.uploadRows[3])*/
            this.uploadRows.forEach(row => this.uploadSingleDiscount(row))
        },
        async getProductId(row) {
            const articleNumber = row["article_number"];
            console.log(articleNumber)
            const productRepository = this.repositoryFactory.create("product");
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals("productNumber", articleNumber));
            await productRepository.search(criteria, Shopware.Context.api)
                .then(result => {
                    if (result.total === 0) {
                        row.productId = "Not Found";
                        return row;
                    }
                    console.log(result);
                    console.log(result.first());
                    console.log(result.first()["id"]);
                    row.productId = result.first()["id"];
                    console.log(row)
                })
            return row

        },
        async onUpload() {

            console.log(this.excelFile);
            const buf = await this.excelFile.arrayBuffer();
            const wb = XLSX.read(buf, { type: 'array' });

            const firstSheet = wb.SheetNames[0];
            const ws = wb.Sheets[firstSheet];

            let rows = XLSX.utils.sheet_to_json(ws, { defval: null });
            await Promise.all(rows.map(row =>  this.getProductId(row)));
            this.uploadRows = rows;
            this.fileUploaded = true;

        },

        onSave() {
            const criteria = new Criteria();

            switch(this.discountTarget) {
                case "allCustomers":
                    this.discount.tagId = null;
                    this.discount.customerId = null;
                case "tag":
                    this.discount.tagId = this.tagId;
                    this.discount.customerId = null;
                    break;
                case "customer":
                    this.discount.tagId = null;
                    this.discount.customerId = this.customerId;
            }
            if (!this.fixedPrice) {

                this.discount.priceReferenceId = this.priceReferenceId;
                console.log("Price Reference is", this.discount.priceReferenceId);
            } else {
                this.discount.priceReferenceId = null;
            }
            console.log(this.discount);
            criteria.addFilter(Criteria.equals("tagId", this.discount.tagId))
            criteria.addFilter(Criteria.equals("customerId", this.discount.customerId))
            criteria.addFilter(Criteria.equals("productId", this.discount.productId))
            this.productDiscountRepository.search(criteria, Shopware.Context.api)
                .then( result => {
                    if (result.total === 0) {
                        console.log("Not Found");
                        this.productDiscountRepository.save(this.discount, Shopware.Context.api);
                    } else {
                        console.log("first ID", result.first().id)
                        this.discount.id = result.first().id;
                        return this.productDiscountRepository.get(result.first().id, Shopware.Context.api)
                            .then( fetchedEntity => {
                                fetchedEntity.productIdId = this.discount.productId;
                                fetchedEntity.userName = this.discount.userName;
                                fetchedEntity.tagId = this.discount.tagId;
                                fetchedEntity.discount = this.discount.discount;
                                fetchedEntity.priceReferenceId = this.discount.priceReferenceId;
                                fetchedEntity.comment = this.discount.comment;
                                this.productDiscountRepository.save(fetchedEntity, Shopware.Context.api)
                            })
                    }
                })

            this.$router.push({ name: 'admintest.discounts.list' })
        },
        async getUser() {
            const user =  await this.userService.getUser();
            const userName = user.data.username
            const fullName = [user.data.firstName, user.data.lastName].filter(Boolean).join(" ");
            return `${userName} // ${fullName}`

        }
    }


}
/*
Component.register('pricetest-index', {
    template,


});*/
