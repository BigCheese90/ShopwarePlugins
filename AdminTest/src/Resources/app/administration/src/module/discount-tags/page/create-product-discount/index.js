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
        discountRepository() {
        return this.repositoryFactory.create("discount_products")
        },
        tagId() {
            return this.$route.params.tagId
        }

    },

    mounted() {
        console.log("ID from route:", this.tagId);
    },
    created() {
        this.discount = this.discountRepository.create();
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
        async uploadSingleDiscount(row) {
            console.log("row starting:", row["article_number"]);
            if (row["productId"] === "Not Found") {
                console.log("Product not found")
                return
            }
            const criteria = new Criteria();
            criteria.addFilter(Criteria.equals("tagId", this.tagIdForUpload))
            criteria.addFilter(Criteria.equals("productId", row["productId"]))

            await this.discountRepository.search(criteria, Shopware.Context.api)
                .then(result => {if (result.total===0) {
                    console.log("Not Found");
                    return this.discountRepository.create(Shopware.Context.api);
                } else {
                    console.log("first ID", result.first().id)
                    return this.discountRepository.get(result.first().id, Shopware.Context.api)
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

                    this.discountRepository.save(discountEntity, Shopware.Context.api);
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
            this.discountRepository.save(this.discount, Shopware.Context.api)
            this.$router.push({ name: 'admintest.discount.tags.list' })
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
