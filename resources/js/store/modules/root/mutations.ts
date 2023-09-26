import { MutationTree } from "vuex";
import { RootMutationsTypes, IRootState } from "./../../interfaces";
import { ROOT_STORE } from "@/store/constants";

export const mutations: MutationTree<IRootState> & RootMutationsTypes = {
    [ROOT_STORE.MUTATIONS.UPDATE_VERSION](state: IRootState, payload: string) {
        state.version = payload;
    },
};
