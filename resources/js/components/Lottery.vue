<template>
    <div>

        <form v-on:submit.prevent="checkValidPromo" class="row justify-content-center">
            <transition name="fade" v-if="!is_valid">
                <div class="col-sm-6 col-md-4 col-12 mb-2">
                    <div class="form_group">
                        <input type="text" placeholder="Введите промокод" name="promocode" v-model="promocode"
                               class="form_control lottery-field" required>
                        <i class="fas fa-terminal"></i>
                    </div>
                </div>
            </transition>

            <div class="col-sm-6 col-md-4 col-12 mb-2">
                <div class="form_group" v-if="!is_valid">
                    <input type="text" placeholder="Введите телефон" name="phone" v-model="phone"
                           required="required" pattern="[\+]\d{2} [\(]\d{3}[\)] \d{3}[\-]\d{2}[\-]\d{2}" maxlength="19"
                           v-mask="['+38 (###) ###-##-##']"
                           class="form_control lottery-field">
                    <i class="fas fa-phone"></i>
                </div>
            </div>
        </form>

        <div class="row justify-content-center">
            <transition name="bounce" v-if="!is_valid">
                <div class="col-sm-6 col-md-3 mb-2 mt-2">
                    <button type="button" class="btn btn-info w-100 p-3" :disabled="phone===null||promocode===null"
                            @click="checkValidPromo">Проверить промокод
                    </button>
                </div>
            </transition>
        </div>


        <div v-if="message.length>0" class="row justify-content-center mb-2 mt-2">

            <div class="col-6">
                <h4 class="text-center text-white" v-html="message"></h4>
            </div>

        </div>
        <!--   <transition name="fade">
               <div class="row justify-content-center mb-5" v-if="canStart">
                   <div class="col-md-3">
                       <button class="btn btn-info btn-lottery" @click="checkValidPromo" v-if="!isWin">Проверить промокод</button>
                       <button class="btn btn-info btn-lottery btn-lottery-green" @click="restart" v-if="isWin">По новой
                       </button>
                   </div>
               </div>
           </transition>-->

        <div v-if="is_valid">
            <transition-group name="flip-list" tag="ul" class="lottery">
                <li class="lottery-item-wrapper" v-for="n in demo_lottery_list" v-bind:key="n"
                    :data-id="n">
                    <div class="lottery-item" @click="openCard()">
                        <img src="https://sun9-35.userapi.com/c858036/v858036636/102217/wYzvw31u87k.jpg"
                             alt="">
                    </div>
                </li>
            </transition-group>
        </div>

        <modal name="winner" :adaptive="true">
            <div class="container container-2">
                <div class="row">
                    <div class="col-12 mb-5">
                        <h4 class="text-center">Ваш приз! Вы автоматически получите его с Вашим заказом!</h4>
                    </div>
                </div>
                <div class="row justify-content-center">
                    <div class="col-6">
                        <h3 class="text-center"><strong>{{prize.title}}</strong></h3>
                        <p><em>{{prize.description}}</em></p>
                    </div>

                </div>
            </div>


        </modal>
        <!--    <ul class="lottery" v-if="code_id!=null&&lottery_list.length>0">
                <li class="lottery-item-wrapper"
                    v-for="lottery_item in lottery_list">
                    <div class="lottery-item">
                        <img :src="lottery_item.image_url"
                             :alt="lottery_item.title">
                        <span class="caption">{{lottery_item.title}}</span>
                    </div>
                </li>
            </ul>-->


    </div>
</template>
<script>
    import {vueTelegramLogin} from 'vue-telegram-login'
    import {mask} from 'vue-the-mask'

    export default {

        data() {
            return {
                demo_lottery_list: [],
                promocode: null,
                phone: null,
                isWin: false,
                is_valid: false,
                message: '',
                prize: {
                    title: '',
                    description: ''
                }
            };
        },

        mounted() {
            for (let i = 1; i <= 30; i++)
                this.demo_lottery_list.push(i);
        },
        methods: {

            getCardsList() {
                this.message = "Загружается список лотов!"
                axios
                    .get('api/users/promo/list')
                    .then(response => {
                        this.lottery_list = response.data.card_list

                        if (this.lottery_list.length > 0)
                            this.canStart = true;
                    });
            },
            sendMessage(message) {
                console.log(message);
                this.$notify({
                    group: 'info',
                    type: 'error',
                    title: 'Оповещение ISUSHI',
                    text: message
                });
            },

            checkValidPromo() {

                if (this.promocode === null) {
                    this.sendMessage("Введите промокод!")
                    return;
                }

                if (this.phone === null) {
                    this.sendMessage("Введите номер телефона!")
                    return;
                }

                if (this.phone.length < 19) {
                    this.sendMessage("Введите корректный номер телефона!")
                    return;
                }

                this.message = "Так-с, проверяем ваш промокод"

                this.shuffle();

                axios
                    .post('api/users/promo/validate', {
                        phone: this.phone,
                        promocode: this.promocode,
                    })
                    .then(response => {
                        this.is_valid = response.data.is_valid
                        this.message = this.is_valid ? "Поздравляем! Ваш промокод подошел!<br>Выберите карточку!" : "Увы, промокод не подошел:("

                    });
            },
            shuffle: function () {

                this.demo_lottery_list = _.shuffle(this.demo_lottery_list)

            },
            show() {
                this.$modal.show('winner');
            },
            hide() {
                this.$modal.hide('winner');
            },
            openCard() {


                if (!this.is_valid) {
                    this.sendMessage("Введите действительный промокод!")
                    return;
                }

                this.message = "Обрабатываем результат розыгрыша!"

                this.is_valid = false

                axios
                    .post(`api/users/promo/check`, {
                        promocode: this.promocode,
                        phone: this.phone,
                    })
                    .then(response => {
                        this.prize = response.data.results[0]
                        this.show()
                        this.sendMessage("Ура! Победили!");

                        this.message = "Ура, победили! Ваш приз: " + this.prize.title;

                        setTimeout(() => {
                            this.$emit("result")
                            this.hide()
                        }, 20000)
                    });
            },


        },
        components: {vueTelegramLogin},
        directives: {mask}
    }
</script>

<style lang="scss">

    .chopcafe_lottery .container-2 {
        background: white;
        padding: 20px;
    }

    .fade-enter-active, .fade-leave-active {
        transition: opacity .5s;
    }

    .fade-enter, .fade-leave-to /* .fade-leave-active до версии 2.1.8 */
    {
        opacity: 0;
    }

    .flip-list-move {
        transition: transform 1s;

    }


    .lottery-field {
        border: 1px #dc3545 solid;
        border-radius: 0;
        padding: 10px;
        height: 50px;
        text-align: center;

        & + i {
            position: absolute;
            left: 31px;
            top: 17px;
            color: #dc3545;
        }
    }

    .btn-lottery {
        background: #dc3545;
        width: 100%;
        height: 47px;
        text-transform: uppercase;
        font-weight: 800;
        border: none;
    }

    .btn-lottery-green {
        background-color: #28a745;
    }

    .lottery-item {
        .caption {

        }
    }

    .btn-info {
        background: #dc3545;
        border: 1px solid #dc3545;

        &[disabled] {
            background: darkgray;
        }

        &:focus,
        &:active,
        &:hover {
            background: darkred !important;
            border: 1px solid darkred !important;
        }
    }


    .bounce-enter-active {
        animation: bounce-in .5s;
    }

    .bounce-leave-active {
        animation: bounce-in .5s reverse;
    }

    @keyframes bounce-in {
        0% {
            transform: scale(0);
        }
        50% {
            transform: scale(1.5);
        }
        100% {
            transform: scale(1);
        }
    }
</style>
