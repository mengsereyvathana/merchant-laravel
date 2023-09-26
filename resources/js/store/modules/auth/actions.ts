import { ActionTree } from "vuex";
import { AuthActionsTypes, AuthStateTypes, IRootState } from "@/store/interfaces";
import { AUTH_STORE } from "@/store/constants";

export const actions: ActionTree<AuthStateTypes, IRootState> & AuthActionsTypes = {
    [AUTH_STORE.ACTIONS.SET_TOKEN]({ commit }, payload: string | null) {
        commit(AUTH_STORE.MUTATIONS.SET_TOKEN, payload as string);
    },
    [AUTH_STORE.ACTIONS.SET_USER_ID]({ commit }, payload: string | null) {
        commit(AUTH_STORE.MUTATIONS.SET_USER_ID, payload as string);
    },
    [AUTH_STORE.ACTIONS.SET_PASSWORD_PASS]({ commit }, payload: string) {
        commit(AUTH_STORE.MUTATIONS.SET_PASSWORD_PASS, payload);
    },
};
