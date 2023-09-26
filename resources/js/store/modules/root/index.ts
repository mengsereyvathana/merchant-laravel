import { Module, ModuleTree } from "vuex";
import { IRootState } from "@/store/interfaces";
import { getters } from "./getters";
import { actions } from "./actions";
import { mutations } from "./mutations";
import { state } from "./state";
import authModule from "../auth";
import cartModule from "../cart";
import searchModule from "../search";

// Modules
const modules: ModuleTree<IRootState> = {
    authModule,
    cartModule,
    searchModule,
};

const root: Module<IRootState, IRootState> = {
    state,
    getters,
    mutations,
    actions,
    modules,
};

export default root;
