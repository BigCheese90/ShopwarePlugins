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
            products: "FZCJ",
            discounts: null,
            result: undefined,
            discount: null,
            isLoading: false,



        }
    },
    computed: {
        discountRepository() {
        return this.repositoryFactory.create("producer_prices")
        },

    },
    created() {
        this.discount = this.discountRepository.create();
        this.getUser().then(user =>
            this.discount.userName = user);
        this.user = this.userService.getUser();
        this.discount.discount = 1;
    },
    methods: {
        onSave() {
            this.discountRepository.save(this.discount, Shopware.Context.api)
            this.$router.push({ name: 'admintest.pricetest.list' })
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
