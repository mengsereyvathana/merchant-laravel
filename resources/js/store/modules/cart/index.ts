import { Module } from "vuex";
import { CartStateTypes, IRootState } from "@/store/interfaces";
import { getters } from "./getters";
import { actions } from "./actions";
import { mutations } from "./mutations";
import { state } from "./state";

// Module
const cart: Module<CartStateTypes, IRootState> = {
    state,
    getters,
    mutations,
    actions,
};

export default cart;
