import { MutationTree } from "vuex";
import { CartMutationsTypes, CartStateTypes } from "./../../interfaces";
import { CART_STORE } from "@/store/constants";
import { ICartItem } from "@/types/Cart";

export const mutations: MutationTree<CartStateTypes> & CartMutationsTypes = {
    [CART_STORE.MUTATIONS.SET_CART](state: CartStateTypes, payload: string) {
        const item = JSON.parse(payload);
        state.cart = item.data;
        state.cartQty = item.amount_qty;
        state.cartAmount = item.amount;
    },
    [CART_STORE.MUTATIONS.ADD_TO_CART](state: CartStateTypes, payload: ICartItem) {
        const product = {
            id: payload.id,
            unit_price: payload.unit_price,
            qty: new Number(1).valueOf(),
            total: payload.total,
        };
        const index = state.cart.findIndex((p) => p.id == product.id);
        if (index >= 0) {
            const existProduct = state.cart[index];
            existProduct.qty += product.qty;
            existProduct.total += product.qty * product.unit_price;
            state.cartAmount += product.unit_price;
            state.cartQty += product.qty;
        } else {
            state.cart.push(product);
            state.cartQty += product.qty;
        }
    },
    [CART_STORE.MUTATIONS.MINUS_FROM_CART](state: CartStateTypes, payload: ICartItem) {
        const product = {
            id: payload.id,
            unit_price: payload.unit_price,
            qty: new Number(1).valueOf(),
            total: payload.total,
        };
        const index = state.cart.findIndex((p) => p.id == product.id);
        if (index >= 0) {
            const existProduct = state.cart[index];
            existProduct.qty -= product.qty;
            existProduct.total -= product.qty * product.unit_price;
            state.cartAmount -= product.unit_price;
            state.cartQty -= product.qty;
        }
    },
    [CART_STORE.MUTATIONS.DELETE_FROM_CART](state: CartStateTypes, index: number) {
        const product = state.cart[index];
        state.cartAmount -= product.total;
        state.cart.splice(index, 1);
        state.cartQty -= product.qty;
    },
    [CART_STORE.MUTATIONS.CLEAR_CART](state: CartStateTypes) {
        state.cart = [];
        state.cartAmount = 0;
        state.cartQty = 0;
    },
};
