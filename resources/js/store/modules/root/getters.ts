import { ROOT_STORE } from "@/store/constants";
import { GetterTree } from "vuex";
import { IRootGettersTypes, IRootState } from "./../../interfaces";

export const getters: GetterTree<IRootState, IRootState> & IRootGettersTypes = {
    [ROOT_STORE.GETTERS.UPDATE_VERSION]: (state: IRootState): string => {
        return state.version;
    },
};
