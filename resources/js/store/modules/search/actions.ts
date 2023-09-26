import { ActionTree } from "vuex";
import { SearchActionsTypes, SearchStateTypes, IRootState } from "@/store/interfaces";
import { SEARCH_STORE } from "@/store/constants";
import { UserID } from "@/services/helper";
import { Http } from "@/services/api/ApiDataService";
import { IProduct } from "@/types/Product";
import { ProductRoute } from "@/services/api/route";
import { productService } from "@/services/api/modules/product.api";

export const actions: ActionTree<SearchStateTypes, IRootState> &
    SearchActionsTypes = {
    async [SEARCH_STORE.ACTIONS.SEARCH_PRODUCT]({ commit }, payload: string) {
        try {
            const data = {
                user_id: UserID.getUser(),
                name: payload,
            };
            const response = await productService.searchProduct(data);
            // if (response.success) {
            //     commit(SEARCH_STORE.MUTATIONS.SET_PRODUCT, response.data);
            // } else {
            //     commit(SEARCH_STORE.MUTATIONS.SET_PRODUCT, []);
            // }
        } catch (error) {
            console.log(error);
        }
    },
};
