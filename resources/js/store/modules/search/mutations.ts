import { MutationTree } from "vuex";
import { SearchMutationsTypes, SearchStateTypes } from "./../../interfaces";
import { SEARCH_STORE } from "@/store/constants";
import { IProductItem } from "@/types/Product";

export const mutations: MutationTree<SearchStateTypes> & SearchMutationsTypes = {
    [SEARCH_STORE.MUTATIONS.SET_PRODUCT](state: SearchStateTypes, payload: IProductItem[]) {
        state.searchProduct = payload;
    },
};
