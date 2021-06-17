<template>
    <div>
        {{cartProducts.length}}
    </div>
</template>
<script>

    export default {
        mounted() {
            this.$store.dispatch("getProductList")
        },
        activated() {
            this.$store.dispatch("getProductList")
        },
        computed: {
            cartProducts: function () {
                return this.$store.getters.cartProducts;
            }
        },


    }
</script>
<style lang="scss">

</style>
