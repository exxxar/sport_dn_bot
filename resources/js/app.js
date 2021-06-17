
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

//import {TinkerComponent} from 'botman-tinker';
import Lottery from '../js/components/Lottery';
import AddToCartBtn from '../js/components/AddCartBtn';
import RollCalc from '../js/components/RollCalc';
import Notifications from 'vue-notification'
import CallbackForm from  '../js/components/CallbackForm';
import CartCountIndex from  '../js/components/CartCountIndex';
import Cart from  '../js/components/Cart';
import NewSliderMenu from  '../js/components/NewSliderMenu';
import VueCurrencyFilter from 'vue-currency-filter'
import store from '../js/store'
import VModal from 'vue-js-modal'

Vue.use(VModal)

Vue.component('lottery', Lottery);
Vue.component('add-to-cart-btn', AddToCartBtn);
Vue.component('roll-calc', RollCalc);
Vue.component('callback-form', CallbackForm);
Vue.component('cart-count-index', CartCountIndex);
Vue.component('cart', Cart);
Vue.component('new-slider-menu', NewSliderMenu);
Vue.use(Notifications)
Vue.use(VueCurrencyFilter,
    {
        symbol : '₽',
        thousandsSeparator: '.',
        fractionCount: 2,
        fractionSeparator: ',',
        symbolPosition: 'back',
        symbolSpacing: true
    })
import Storage from 'vue-ls';

let options = {
    namespace: 'vuejs__', // key prefix
    name: 'ls', // name variable Vue.[ls] or this.[$ls],
    storage: 'local', // storage name session, local, memory
};

Vue.use(Storage, options);

const app = new Vue({
    store,
    el: '#app'
});
