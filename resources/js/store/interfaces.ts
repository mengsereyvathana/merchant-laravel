import { ActionContext, DispatchOptions } from "vuex";
import { AUTH_STORE, CART_STORE, ROOT_STORE, SEARCH_STORE } from "./constants";
import { ICartItem } from "@/types/Cart";
import { IProductItem } from "@/types/Product";

export interface IRootState {
    root: boolean;
    version: string;
}

export interface IMergedState extends IRootState {
    authModule: AuthStateTypes;
    cartModule: CartStateTypes;
    searchModule: SearchStateTypes;
}

export interface IRootGettersTypes {
    [ROOT_STORE.GETTERS.UPDATE_VERSION](state: IRootState): string;
}

export type RootMutationsTypes<S = IRootState> = {
    [ROOT_STORE.MUTATIONS.UPDATE_VERSION](state: S, payload: string): void;
};

type AugmentedActionContextRoot = {
    commit<K extends keyof RootMutationsTypes>(key: K, payload: Parameters<RootMutationsTypes[K]>[1]): ReturnType<RootMutationsTypes[K]>;
} & Omit<ActionContext<IRootState, IRootState>, "commit"> & {
        dispatch<K extends keyof StoreActions>(key: K, payload?: Parameters<StoreActions[K]>[1], options?: DispatchOptions): ReturnType<StoreActions[K]>;
    };

export interface RootActionsTypes {
    [ROOT_STORE.ACTIONS.UPDATE_VERSION]({ commit }: AugmentedActionContextRoot, payload: string): void;
}

/*********************** AUTH MODULE TYPES  ***********************/
export interface AuthStateTypes {
    token: string | null;
    userId: string | null;
    passwordPass: string;
}

export interface AuthGettersTypes {
    [AUTH_STORE.GETTERS.GET_TOKEN](state: AuthStateTypes): string | null;
    [AUTH_STORE.GETTERS.GET_USER_ID](state: AuthStateTypes): string | null;
    [AUTH_STORE.GETTERS.GET_PASSWORD_PASS](state: AuthStateTypes): string;
}

export interface AuthMutationsTypes<S = AuthStateTypes> {
    [AUTH_STORE.MUTATIONS.SET_TOKEN](state: S, payload: string | null): void;
    [AUTH_STORE.MUTATIONS.SET_USER_ID](state: S, payload: string | null): void;
    [AUTH_STORE.MUTATIONS.SET_PASSWORD_PASS](state: S, payload: string): void;
}

type AugmentedActionContextAuth = {
    commit<K extends keyof AuthMutationsTypes>(key: K, payload: Parameters<AuthMutationsTypes[K]>[1]): ReturnType<AuthMutationsTypes[K]>;
} & Omit<ActionContext<AuthStateTypes, IRootState>, "commit">;

export interface AuthActionsTypes {
    [AUTH_STORE.ACTIONS.SET_TOKEN]({ commit }: AugmentedActionContextAuth, payload: string | null): void;
    [AUTH_STORE.ACTIONS.SET_USER_ID]({ commit }: AugmentedActionContextAuth, payload: string | null): void;
    [AUTH_STORE.ACTIONS.SET_PASSWORD_PASS]({ commit }: AugmentedActionContextAuth, payload: string): void;
}

/*********************** CART MODULE TYPES  ***********************/
export interface CartStateTypes {
    cart: ICartItem[];
    cartQty: number;
    cartAmount: number;
}
export interface CartGettersTypes {
    [CART_STORE.GETTERS.GET_CART](state: CartStateTypes): ICartItem[];
    [CART_STORE.GETTERS.GET_QTY](state: CartStateTypes): number;
    [CART_STORE.GETTERS.GET_CART_AMOUNT](state: CartStateTypes): number;
}

export interface CartMutationsTypes<S = CartStateTypes> {
    [CART_STORE.MUTATIONS.SET_CART](state: S, payload: string): void;
    [CART_STORE.MUTATIONS.ADD_TO_CART](state: S, payload: ICartItem): void;
    [CART_STORE.MUTATIONS.MINUS_FROM_CART](state: S, payload: ICartItem): void;
    [CART_STORE.MUTATIONS.DELETE_FROM_CART](state: S, payload: number): void;
    [CART_STORE.MUTATIONS.CLEAR_CART](state: S, payload?: number): void;
}

type AugmentedActionContextCart = {
    commit<K extends keyof CartMutationsTypes>(key: K, payload: Parameters<CartMutationsTypes[K]>[1]): ReturnType<CartMutationsTypes[K]>;
} & Omit<ActionContext<CartStateTypes, IRootState>, "commit">;

export interface CartActionsTypes {
    [CART_STORE.ACTIONS.GET_CART_ITEM_API]({ commit }: AugmentedActionContextCart, payload: object): Promise<void>;
    [CART_STORE.ACTIONS.ADD_TO_CART]({ commit }: AugmentedActionContextCart, payload: object): Promise<void>;
    [CART_STORE.ACTIONS.MINUS_FROM_CART]({ commit }: AugmentedActionContextCart, payload: object): Promise<void>;
    [CART_STORE.ACTIONS.DELETE_FROM_CART]({ commit }: AugmentedActionContextCart, payload: object): Promise<void>;
}

/*********************** SEARCH MODULE TYPES  ***********************/
export interface SearchStateTypes {
    searchProduct: IProductItem[];
}
export interface SearchGettersTypes {
    [SEARCH_STORE.GETTERS.GET_PRODUCT](state: SearchStateTypes): IProductItem[];
}

export interface SearchMutationsTypes<S = SearchStateTypes> {
    [SEARCH_STORE.MUTATIONS.SET_PRODUCT](state: S, payload: IProductItem[]): void;
}

type AugmentedActionContextSearch = {
    commit<K extends keyof SearchMutationsTypes>(key: K, payload: Parameters<SearchMutationsTypes[K]>[1]): ReturnType<SearchMutationsTypes[K]>;
} & Omit<ActionContext<SearchStateTypes, IRootState>, "commit">;

export interface SearchActionsTypes {
    [SEARCH_STORE.ACTIONS.SEARCH_PRODUCT]({ commit }: AugmentedActionContextSearch, payload: string): Promise<void>;
}

//
export interface StoreActions extends AuthActionsTypes, CartActionsTypes, SearchActionsTypes, RootActionsTypes {}

export interface StoreGetters extends AuthGettersTypes, CartGettersTypes, SearchGettersTypes, IRootGettersTypes {}
