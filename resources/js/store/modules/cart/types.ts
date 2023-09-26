import { CartStateTypes, CartMutationsTypes, CartGettersTypes, CartActionsTypes } from "@/store/interfaces";
import { Store as VuexStore, CommitOptions, DispatchOptions } from "vuex";

export type CartStoreModuleTypes<S = CartStateTypes> = Omit<VuexStore<S>, "commit" | "getters" | "dispatch"> & {
    commit<K extends keyof CartMutationsTypes, P extends Parameters<CartMutationsTypes[K]>[1]>(key: K, payload?: P, options?: CommitOptions): ReturnType<CartMutationsTypes[K]>;
} & {
    getters: {
        [K in keyof CartGettersTypes]: ReturnType<CartGettersTypes[K]>;
    };
} & {
    dispatch<K extends keyof CartActionsTypes>(key: K, payload?: Parameters<CartActionsTypes[K]>[1], options?: DispatchOptions): ReturnType<CartActionsTypes[K]>;
};
