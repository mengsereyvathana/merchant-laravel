import { Module } from "vuex";
import { SearchStateTypes, IRootState } from "@/store/interfaces";
import { getters } from "./getters";
import { actions } from "./actions";
import { mutations } from "./mutations";
import { state } from "./state";

// Module
const search: Module<SearchStateTypes, IRootState> = {
    state,
    getters,
    mutations,
    actions,
};

export default search;
