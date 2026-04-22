import template from './manufacturer-discount.html.twig';


const { Criteria } = Shopware.Data;

const { Component } = Shopware;
console.log(Shopware)
export default {
    template,
    inject: [
        "userService",
        "repositoryFactory"
    ],
    data() {
        return {

            discountTarget: null,
            // discounts: null,
            result: undefined,
            discount: null,
            isLoading: false,
            tagName: "Alle Kunden",
            tagId: null,
            priceReferenceId: null,
            customerId: null,
            alternativePrice: null,
        }
    },
    computed: {
        // customFieldName() {
        //     if (this.priceReference == null) {
        //         console.log("nopricefound");
        //         return null
        //     }
        //     const repository = this.repositoryFactory.create("custom_field");
        //     const criteria  = new Criteria();
        //     criteria.addFilter(Criteria.equals("id", this.priceReference))
        //     repository.search(criteria, Shopware.Context.api)
        //         .then(result => {
        //             this.discount.priceReference = result.first().name;
        //             console.log(result.first().name)
        //             return result.first().name
        //         })
        //
        // },
        customFieldCriteria() {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.contains("name", "custom_product_pricing"));
            return criteria;
        },
        discountManufacturerRepository() {
        return this.repositoryFactory.create("discount_manufacturer")
        },
        tagId() {
            return this.$route.params.tagId
        },
        tagRepository() {
            return this.repositoryFactory.create("tag")
        },
        customerCriteria() {
            const criteria = new Criteria();
            criteria.addAssociation('defaultBillingAddress')
            return criteria
        },
        showInputFields() {
            if (this.discountTarget=="allCustomers") {
                return true
            }
            if (this.discountTarget=="tag" && this.tagId) {
                return true
            }
            if (this.discountTarget=="customer" && this.customerId) {
                return true
            }
        }
    },

    mounted() {
        console.log("ID from route:", this.tagId);
    },
    created() {
        this.discount = this.discountManufacturerRepository.create();
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
                    console.log(result.first().name)
                })
        }
    },
    watch: {
        priceReferenceId(newId) {
            if (!newId) {
                this.selectedLabel = null;
                return;
            }

            // Access the internal collection of the select component
            const selection = this.$refs.customFieldSelect.resultCollection;
            console.log(selection);
            const entity = selection.get(newId);
            console.log(entity)

            if (entity) {
                // Path to the label in the custom_field entity
                this.selectedLabel = entity.config?.label['en-GB'] || entity.name;
                console.log('Successfully extracted label:', this.selectedLabel);
            }
        }
    },
    methods: {
        onSelectCustomField(item) {
            if (!item) {
                console.log("no item selected")
                this.priceReferenceLabel = null;
                return;
            }

            // Access the nested label property
            const label = item

            // Now you have the string value to use in your Vue logic
            this.priceReferenceLabel = label;
            console.log("Something selected");
            console.log('Selected Label:', label);
            return
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
            if (this.alternativePrice) {

                this.discount.priceReferenceId = this.priceReferenceId;
                console.log("Price Reference is", this.discount.priceReferenceId);
            } else {
                this.discount.priceReferenceId = null;
            }

            criteria.addFilter(Criteria.equals("tagId", this.discount.tagId))
            criteria.addFilter(Criteria.equals("customerId", this.discount.customerId))
            criteria.addFilter(Criteria.equals("manufacturerId", this.discount.manufacturerId))
            this.discountManufacturerRepository.search(criteria, Shopware.Context.api)
                .then( result => {
                    if (result.total === 0) {
                        console.log("Not Found");
                        this.discountManufacturerRepository.save(this.discount, Shopware.Context.api);
                    } else {
                        console.log("first ID", result.first().id)
                        this.discount.id = result.first().id;
                        return this.discountManufacturerRepository.get(result.first().id, Shopware.Context.api)
                            .then( fetchedEntity => {
                                fetchedEntity.manufacturerId = this.discount.manufacturerId;
                                fetchedEntity.userName = this.discount.userName;
                                fetchedEntity.tagId = this.discount.tagId;
                                fetchedEntity.discount = this.discount.discount;
                                fetchedEntity.priceReferenceId = this.discount.priceReferenceId;
                                fetchedEntity.comment = this.discount.comment;
                                this.discountManufacturerRepository.save(fetchedEntity, Shopware.Context.api)
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
        customerLabelProperty() {
            return ["customerNumber", "defaultBillingAddress.company", ]
        },
    }


}
/*
Component.register('pricetest-index', {
    template,


});*/
