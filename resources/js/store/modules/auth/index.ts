import { Module } from "vuex";
import { AuthStateTypes, IRootState } from "@/store/interfaces";
import { getters } from "./getters";
import { actions } from "./actions";
import { mutations } from "./mutations";
import { state } from "./state";

// Module
const auth: Module<AuthStateTypes, IRootState> = {
    state,
    getters,
    mutations,
    actions,
};

export default auth;
