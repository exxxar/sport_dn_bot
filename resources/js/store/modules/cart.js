const state = {
    items: [],
}

// getters
const getters = {
    cartProducts: (state, getters, rootState) => {
        return state.items;
    },
    cartTotalCount: (state, getters) => {
        let summ = 0;
        state.items.forEach((item)=>{
            summ+=item.quantity
        });
        return summ
    },
    cartTotalPrice: (state, getters) => {
        let summ = 0;
        state.items.forEach((item)=>{
            summ+=item.product.price*item.quantity
        });
        return summ
    }
}

// actions
const actions = {
    getProductList({state, commit}) {
        return state.items
    },
    addProductToCart({state, commit}, product) {
        commit('pushProductToCart', product);
    },
    incQuantity({state, commit}, id) {
        commit('incrementItemQuantity', id);
    },
    decQuantity({state, commit}, id) {
        commit('decrementItemQuantity', id);
    },
    removeProduct({state, commit}, id) {
        commit('removeItem', id);
    },
    clearCart({state, commit}) {
        commit('clearAllItems');
    }
}

// mutations
const mutations = {
    pushProductToCart(state, product) {
        const cartItem = state.items.find(item => item.product.id === product.id)
        if (!cartItem)
            state.items.push({
                product,
                quantity: 1
            })
        else
            cartItem.quantity++;
    },

    incrementItemQuantity(state, id) {
        const cartItem = state.items.find(item => item.product.id === id)
        cartItem.quantity++
    },

    decrementItemQuantity(state, id) {
        const cartItem = state.items.find(item => item.product.id === id)
        if (cartItem.quantity > 1)
            cartItem.quantity--;
    },
    removeItem(state, id) {
        let tmp = state.items.filter((item) => item.product.id !== id);
        state.items = tmp
        //commit('setCartItems',tmp)
    },

    clearAllItems(state) {
        state.items = []
        //commit('setCartItems',tmp)
    },
    setCartItems(state, {items}) {
        state.items = items
    },

}

export default {
    state,
    getters,
    actions,
    mutations
}
