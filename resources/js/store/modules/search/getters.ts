import { SEARCH_STORE } from "@/store/constants";
import { GetterTree } from "vuex";
import { SearchGettersTypes, SearchStateTypes, IRootState } from "./../../interfaces";

export const getters: GetterTree<SearchStateTypes, IRootState> & SearchGettersTypes = {
    [SEARCH_STORE.GETTERS.GET_PRODUCT]: (state: SearchStateTypes) => {
        return state.searchProduct;
    },
};
