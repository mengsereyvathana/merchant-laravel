import { ActionTree } from "vuex";
import { RootActionsTypes, IRootState } from "@/store/interfaces";
import { ROOT_STORE } from "@/store/constants";

export const actions: ActionTree<IRootState, IRootState> & RootActionsTypes = {
    [ROOT_STORE.ACTIONS.UPDATE_VERSION]({ commit }, payload: string) {
        commit(ROOT_STORE.MUTATIONS.UPDATE_VERSION, payload);
    },
};
