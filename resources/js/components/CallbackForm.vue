<template>
    <div>
        <form @submit="sendRequest">
            <div class="row">
                <div class="col-lg-6">
                    <div class="form_group"><input type="text" placeholder="Ваше имя" name="name" v-model="name"
                                                   required="required"
                                                   class="form_control"> <i class="fas fa-user"></i></div>
                </div>
                <div class="col-lg-6">
                    <div class="form_group"><input type="text" placeholder="Ваш номер телефона" name="phone"
                                                   v-model="phone"
                                                   required="required" class="form_control phone" maxlength="15"> <i
                            class="fas fa-phone"></i></div>
                </div>
                <div class="col-lg-12">
                    <div class="form_group"><textarea placeholder="Сообщение для нас" name="message" v-model="message"
                                                      class="form_control"></textarea></div>
                </div>
                <div class="col-lg-12">
                    <div class="form_button text-center">
                        <button class="chopcafe_btn form_btn">Оформить заявку</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</template>
<script>
    export default {
        data() {
            return {
                name: '',
                phone: '',
                message: ''
            };
        },
        methods: {
            sendRequest: function (e) {
                e.preventDefault();
                axios
                    .post('api/send-request', {
                        name: this.name,
                        phone: this.phone,
                        message: this.message
                    })
                    .then(response => {
                        this.sendMessage("Сообщение успешно отправлено");
                    });
            },
            sendMessage(message) {
                this.$notify({
                    group: 'info',
                    type: 'success',
                    title: 'Отправка сообщений ISUSHI',
                    text: message
                });
            },
        }
    }
</script>
