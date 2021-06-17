<template>
    <div>
        <slick
            ref="slick"
            :options="slickOptions">
            <a target="_blank" href="https://psv4.userapi.com/c856432/u62408223/docs/d16/ad03d46aa9f6/1.jpg"><img src="https://psv4.userapi.com/c856432/u62408223/docs/d16/ad03d46aa9f6/1.jpg" alt=""></a>
            <a target="_blank" href="https://psv4.userapi.com/c856432/u62408223/docs/d12/bdd6d28de21f/2.jpg"><img src="https://psv4.userapi.com/c856432/u62408223/docs/d12/bdd6d28de21f/2.jpg" alt=""></a>
            <a target="_blank" href="https://psv4.userapi.com/c856432/u62408223/docs/d12/a3bb8a0a624e/3.jpg"><img src="https://psv4.userapi.com/c856432/u62408223/docs/d12/a3bb8a0a624e/3.jpg" alt=""></a>

        </slick>
       <!-- <hr>
        <h4 class="text-center">Так-с, что мы добавим в корзину?</h4>
        <div class="row d-flex justify-content-center">
            <div class="col-4 controlls">
                <h5 class="text-center  mb-2 mt-5">Сюда вводим номер из меню</h5>
                <div class="input-group">

                    <input type="number" min="0" max="1000" class="form-control text-center" v-model="product_id" placeholder="Номер товара из меню">
                </div>
                <h5 class="text-center  mb-2 mt-3">Сюда вводим колличество</h5>
                <div class="input-group">

                     <input type="number" min="0" max="1000" class="form-control text-center" v-model="count" placeholder="Колличество">
                </div>

                <div class="input-group mt-2">
                    <a class="chopcafe_btn about_btn text-white text-center w-100">Добавить</a>
                </div>


            </div>
        </div>
-->
    </div>
</template>
<script>
    import Slick from 'vue-slick';


    export default {
        components: { Slick },

        data() {
            return {
                product_id:null,
                count:0,
                slickOptions: {
                    slidesToShow: 1,
                    infinite: true,
                    dots: true,
                    arrows: true,
                },
            };
        },
    }
</script>

<style lang="scss">
    .slick-slide img {
        display: block;
        object-fit: cover;
        width: 100%;
        height: 100%;
        padding: 10px;
    }

    .controlls {
        input{
            padding: 25px;
        }

        button {
            padding:15px;
        }
    }

    .chopcafe_btn {
        cursor: pointer;
    }

    .slick-prev {
        background: red;
        width: 20px;
        height: 20px;
        color: black;
        position: absolute;
        left: 0;
        border-radius: 5px 0px 0px 5px;
    }

    .slick-next {
        background: red;
        width: 20px;
        height: 20px;
        position: absolute;
        color: black;
        right: 0;
        border-radius: 0px 5px 5px 0px;
    }


</style>