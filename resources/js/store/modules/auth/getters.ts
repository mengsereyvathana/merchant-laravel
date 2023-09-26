import { AUTH_STORE } from "@/store/constants";
import { GetterTree } from "vuex";
import { AuthGettersTypes, AuthStateTypes, IRootState } from "./../../interfaces";

export const getters: GetterTree<AuthStateTypes, IRootState> & AuthGettersTypes = {
    [AUTH_STORE.GETTERS.GET_TOKEN]: (state: AuthStateTypes) => {
        return state.token;
    },
    [AUTH_STORE.GETTERS.GET_USER_ID]: (state: AuthStateTypes) => {
        return state.userId;
    },
    [AUTH_STORE.GETTERS.GET_PASSWORD_PASS]: (state: AuthStateTypes) => {
        return state.passwordPass;
    },
};
