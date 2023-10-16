import { ActionTree } from "vuex";
import { SearchActionsTypes, SearchStateTypes, IRootState } from "@/store/interfaces";
import { SEARCH_STORE } from "@/store/constants";
import { UserID } from "@/services/helper";
import { IProduct } from "@/types/Product";
import { productService } from "@/services/api/modules/product.api";

export const actions: ActionTree<SearchStateTypes, IRootState> &
    SearchActionsTypes = {
    async [SEARCH_STORE.ACTIONS.SEARCH_PRODUCT]({ commit }, payload: string) {
        try {
            const params = {
                user_id: UserID.getUser(),
                name: payload,
            };
            const [error, data] = await productService.searchProducts(params);
            if (error) console.log(error)
            else {
                if (data.success) {
                    commit(SEARCH_STORE.MUTATIONS.SET_PRODUCT, data.data);
                } else {
                    commit(SEARCH_STORE.MUTATIONS.SET_PRODUCT, []);
                }
            }
        } catch (error) {
            console.log(error);
        }
    },
};
