import { CART_STORE } from "@/store/constants";
import { GetterTree } from "vuex";
import { CartGettersTypes, CartStateTypes, IRootState } from "./../../interfaces";

export const getters: GetterTree<CartStateTypes, IRootState> & CartGettersTypes = {
    [CART_STORE.GETTERS.GET_CART]: (state: CartStateTypes) => {
        return state.cart;
    },
    [CART_STORE.GETTERS.GET_QTY]: (state: CartStateTypes) => {
        return state.cartQty;
    },
    [CART_STORE.GETTERS.GET_CART_AMOUNT]: (state: CartStateTypes) => {
        return state.cartAmount;
    },
};
