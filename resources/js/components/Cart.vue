<template>
    <div class="cart">

        <div class="container" v-if="cartProducts.length>0">
            <div class="row cart-table">
                <div class="col-lg-12">
                    <div class="chopcafe_product_table">
                        <table class="chopcafe_table chopcafe_custom_table table">
                            <thead>
                            <tr>
                                <th>Изображение</th>
                                <th>Название продукта</th>
                                <th>Цена</th>
                                <th>Количество</th>
                                <th>Всего</th>
                                <th>Удалить</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr v-for="item in cartProducts">
                                <td class="thumbnail">
                                    <img :src="item.product.image_url" class="img-fluid" alt="">
                                </td>
                                <td class="product_title"><a href="shop_details.html">{{item.product.title}}</a>
                                </td>
                                <td class="product_price"><span>{{item.product.price | currency}}</span></td>
                                <td class="product_quantity">
                                    <div class="row justify-content-center">
                                        <div class="col-sm-2 col-lg-2">
                                            <button type="button" class="btn btn-primary" :disabled="item.quantity===1"
                                                    @click="decrement(item.product.id)">-
                                            </button>

                                        </div>

                                        <div class="col-sm-4 col-lg-4">
                                            <input type="number" :value="item.quantity" step="1" min="0"
                                                   class="form-control">

                                        </div>
                                        <div class="col-sm-2 col-lg-2">
                                            <button type="button" class="btn btn-primary"
                                                    @click="increment(item.product.id)">+
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td class="total">
                                    <span>{{item.product.price*item.quantity | currency }}</span>
                                </td>
                                <td class="product_delete">
                                    <a href="#" @click="remove(item.product.id)"><i class="fas fa-trash-alt"></i></a>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <div class="billing_order_confirm">
                        <table class="table chopcafe_table">
                            <thead>
                            <tr>
                                <th class="cart_title" colspan="2">Итого</th>
                            </tr>
                            </thead>
                            <tbody>
                            <tr>
                                <td class="subtotal"></td>
                                <td class="subtotal_price"><span>Всего</span></td>
                            </tr>
                            <tr>
                                <td class="product"><span>Количество</span></td>
                                <td class="price"><span>{{cartTotalCount}}</span></td>
                            </tr>
                            <tr>
                                <td class="shipping"><span>Доставка</span></td>
                                <td class="shipping_info"><span>{{delivery_price|currency}}</span></td>
                            </tr>
                            <tr>
                                <td class="total_price"><span>Цена заказа</span></td>
                                <td class="price"><span>{{cartTotalPrice | currency}}</span></td>
                            </tr>
                            <tr>
                                <td class="total_price"><span>Цена заказа с доставкой</span></td>
                                <td class="price"><span>{{cartTotalPrice+delivery_price | currency}}</span></td>
                            </tr>
                            </tbody>
                        </table>

                        <form v-on:submit.prevent="requestRange" class="row mt-2">
                            <div class="col-lg-12 mt-2">
                                <div class="form_group">
                                    <input type="text" placeholder="Ваш адрес" name="address"
                                           v-model="address" class="form_control"
                                           required="required">
                                </div>
                            </div>

                            <div class="col-lg-12 mt-2">
                                <div class="form_group">
                                    <p>{{range_message}}</p>
                                    <button type="submit" class="chopcafe_btn continue_btn"><i
                                            class="fas fa-shopping-cart"></i>Расчитать цену доставки
                                    </button>
                                </div>
                            </div>

                        </form>

                        <hr>
                        <div class="row mt-2">
                            <div class="col-lg-6 col-sm-12">
                                <div class="update_cart">
                                    <a href="#" class="chopcafe_btn clear_cart_btn" @click="clearCart"><i
                                            class="fas fa-times-circle"></i>Очистить
                                        корзину</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <form @submit="sendRequest">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="form_group">
                                    <input type="text" placeholder="Ваше имя" name="name"
                                           v-model="name"
                                           required="required"
                                           class="form_control">
                                </div>
                            </div>
                            <div class="col-lg-12 mt-2">
                                <div class="form_group">
                                    <input type="text" placeholder="Ваш номер телефона" name="phone"
                                           v-model="phone"
                                           required="required" pattern="[\+]\d{2} [\(]\d{3}[\)] \d{3}[\-]\d{2}[\-]\d{2}"
                                           class="form_control" maxlength="19"
                                           v-mask="['+38 (###) ###-##-##']">
                                </div>
                            </div>


                            <div class="col-lg-12 mt-2">
                                <div class="form_group">
                                    <textarea style="height: 122px;" placeholder="Сообщение для нас"
                                              name="message"
                                              v-model="message"
                                              class="form_control"></textarea>
                                </div>
                            </div>
                            <div class="col-lg-12 mt-2">
                                <div class="continue_shopping">
                                    <button class="chopcafe_btn continue_btn" :disabled="sending||delivery_price===0"><i
                                            class="fas fa-shopping-cart"></i>Оформить
                                        покупку
                                    </button>
                                </div>
                            </div>


                        </div>
                    </form>

                </div>
            </div>
        </div>
        <div v-if="cartProducts.length===0">
            <div class="row justify-content-center">
                <img :src="'../assets/images/empty_cart.png'" alt="">
            </div>
            <h1 class="text-center">Корзина пуста:(</h1>
        </div>

   <!--     <div v-if="promocode.trim().length>0">
            <hr>
            <h4 class="text-center">Ваш промокод </h4>
            <br>
            <p class="text-center"><strong>{{promocode}}</strong><a href="#removeCode" @click="removeCode">(удалить)</a></p>
            <br>
            <section class="chopcafe_lottery chopcafe_about_2 section_padding_2 s3">
                <div class="container">
                    <div class="chopcafe_title chopcafe_title_1 overlay_title text-center">
                        <h2>Розыгрыш <span>призов</span></h2>
                        <div class="title_divider"><i class="flaticon-fork-and-knife-in-cross title_fork"></i></div>
                    </div>
                    <lottery v-on:result="removeCode"></lottery>
                </div>
            </section>

        </div>-->

    </div>
</template>
<script>
    import {mask} from 'vue-the-mask'

    export default {
        data() {
            return {
                phone: '',
                name: '',
                address: '',
                range: 0,
                message: '',
                range_message: '',
                delivery_price: 0,
                sending: false,
                promocode: localStorage.getItem("sushi_promocode")===null?'':localStorage.getItem("sushi_promocode"),
            }
        },
        mounted() {
            this.$store.dispatch("getProductList")
        },
        activated() {
            this.$store.dispatch("getProductList")
        },
        computed: {
            cartProducts: function () {
                return this.$store.getters.cartProducts;
            },
            cartTotalCount: function () {
                return this.$store.getters.cartTotalCount;
            },
            cartTotalPrice: function () {
                return this.$store.getters.cartTotalPrice;
            }
        },

        methods: {

            removeCode(){
                this.sendMessage("Промокод удален!");

                this.promocode = ''
                localStorage.setItem("sushi_promocode",  this.promocode);
            },
            requestRange() {
                axios.post('/api/range', {
                    address: this.address
                }).then(resp => {
                    this.range = resp.data.range
                    this.delivery_price = resp.data.price
                    this.range_message = `Цена доставки по вашему адресу составит ${this.delivery_price} руб.`
                })

            },
            sendRequest(e) {
                e.preventDefault();

                if (this.delivery_price === 0) {
                    this.sendMessage("Сперва расчитайте цену доставки!");
                    return;
                }

                this.sending = true;
                let products = '';

                this.cartProducts.forEach(function (item) {
                    if (item.product.category !== '#соберисам')
                        products += item.product.title + "_#" + item.product.id + "_ x  " + item.quantity + "штук => " + item.quantity * item.product.price + "₽\n"
                    else
                        products += item.product.title + " х  " + item.quantity + "штук \nОписание:\n" + item.product.description + "*Цена => " + item.quantity * item.product.price + "₽*\n\n"
                });


                this.promocode = ''
                localStorage.setItem("sushi_promocode",  this.promocode);

                let message = `*Заказ с сайта (isushi):*\n${products}\n_${this.message}_\nЦена заказа:*${this.cartTotalPrice}₽*\nЦена доставки:*${this.delivery_price}₽*\nАдрес доставки: ${this.address}\nСуммарно: *${this.cartTotalPrice + this.delivery_price} ₽*`;
                axios
                    .post('api/send-request', {
                        name: this.name,
                        phone: this.phone,
                        message: message,
                        summary_price: this.cartTotalPrice
                    })
                    .then(response => {
                        this.sendMessage("Заказ успешно отправлен");
                        this.sending = false;
                        this.clearCart()

                        this.promocode = response.data.code
                        localStorage.setItem("sushi_promocode",  this.promocode);

                    });
            },
            sendMessage(message) {
                this.$notify({
                    group: 'messages',
                    type: 'success',
                    title: 'Отправка заказа ISUSHI',
                    text: message
                });
            },
            increment(id) {
                this.$store.dispatch("incQuantity", id)
            },
            decrement(id) {
                this.$store.dispatch("decQuantity", id)
            },
            remove(id) {
                this.$store.dispatch("removeProduct", id)
            },
            clearCart() {
                this.$store.dispatch("clearCart")
            }
        },
        directives: {mask}

    }
</script>
<style lang="scss">
    .chopcafe_custom_table {
        min-width: 1000px;
    }

    .chopcafe_btn[disabled] {
        background: gray;
    }

    .cart-table {
        overflow-x: auto;
    }
</style>
