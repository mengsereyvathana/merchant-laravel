import { AuthActions, AuthMutations, AuthGetters, CartActions, CartMutations, CartGetters, SearchGetters, SearchMutations, SearchActions, RootGetters, RootMutations, RootActions } from "./enums";

export const AUTH_STORE = {
    GETTERS: AuthGetters,
    MUTATIONS: AuthMutations,
    ACTIONS: AuthActions,
};

export const CART_STORE = {
    GETTERS: CartGetters,
    MUTATIONS: CartMutations,
    ACTIONS: CartActions,
};

export const SEARCH_STORE = {
    GETTERS: SearchGetters,
    MUTATIONS: SearchMutations,
    ACTIONS: SearchActions,
};

export const ROOT_STORE = {
    GETTERS: RootGetters,
    MUTATIONS: RootMutations,
    ACTIONS: RootActions,
};
