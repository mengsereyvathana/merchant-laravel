import { MutationTree } from "vuex";
import { AuthMutationsTypes, AuthStateTypes } from "./../../interfaces";
import { AUTH_STORE } from "@/store/constants";

export const mutations: MutationTree<AuthStateTypes> & AuthMutationsTypes = {
    [AUTH_STORE.MUTATIONS.SET_TOKEN](state: AuthStateTypes, payload: string | null) {
        state.token = payload;
    },
    [AUTH_STORE.MUTATIONS.SET_USER_ID](state: AuthStateTypes, payload: string | null) {
        state.userId = payload;
    },
    [AUTH_STORE.MUTATIONS.SET_PASSWORD_PASS](state: AuthStateTypes, payload: string) {
        state.passwordPass = payload;
    },
};
