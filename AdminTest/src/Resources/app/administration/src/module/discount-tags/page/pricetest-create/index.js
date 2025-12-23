import template from './pricetest-create.html.twig';

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
            priceReference: null,
            discounts: null,
            result: undefined,
            discount: null,
            isLoading: false,
            tagName: "Alle Kunden",
        }
    },
    computed: {
        customFieldName() {
            if (this.priceReference == null) {
                console.log("nopricefound");
                return null
            }
            const repository = this.repositoryFactory.create("custom_field");
            const criteria  = new Criteria();
            criteria.addFilter(Criteria.equals("id", this.priceReference))
            repository.search(criteria, Shopware.Context.api)
                .then(result => {
                    this.discount.priceReference = result.first().name;
                    console.log(result.first().name)
                    return result.first().name
                })

        },
        customFieldCriteria() {
            const criteria = new Criteria();
            criteria.addFilter(Criteria.contains("name", "custom_product_pricing"));
            return criteria;
        },
        discountRepository() {
        return this.repositoryFactory.create("producer_prices")
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
                    console.log(result.first().name)
                })
        }
    },
    methods: {
        onSave() {
            const criteria = new Criteria();
            if (this.discount.tagId != null) {
                criteria.addFilter(Criteria.equals("tagId", this.discount.tagId))
            }

            criteria.addFilter(Criteria.equals("manufacturerId", this.discount.manufacturerId))
            this.discountRepository.search(criteria, Shopware.Context.api)
                .then( result => {
                    if (result.total === 0) {
                        console.log("Not Found");
                        this.discountRepository.save(this.discount, Shopware.Context.api);
                    } else {
                        console.log("first ID", result.first().id)
                        this.discount.id = result.first().id;
                        return this.discountRepository.get(result.first().id, Shopware.Context.api)
                            .then( fetchedEntity => {
                                fetchedEntity.manufacturerId = this.discount.manufacturerId;
                                fetchedEntity.userName = this.discount.userName;
                                fetchedEntity.tagId = this.discount.tagId;
                                fetchedEntity.discount = this.discount.discount;
                                fetchedEntity.priceReference = this.discount.priceReference;
                                fetchedEntity.comment = this.discount.comment;
                                this.discountRepository.save(fetchedEntity, Shopware.Context.api)
                            })
                    }
                })

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
