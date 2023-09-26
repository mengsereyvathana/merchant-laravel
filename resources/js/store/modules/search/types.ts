import { SearchStateTypes, SearchMutationsTypes, SearchGettersTypes, SearchActionsTypes } from "@/store/interfaces";
import { Store as VuexStore, CommitOptions, DispatchOptions } from "vuex";

export type SearchStoreModuleTypes<S = SearchStateTypes> = Omit<VuexStore<S>, "commit" | "getters" | "dispatch"> & {
    commit<K extends keyof SearchMutationsTypes, P extends Parameters<SearchMutationsTypes[K]>[1]>(key: K, payload?: P, options?: CommitOptions): ReturnType<SearchMutationsTypes[K]>;
} & {
    getters: {
        [K in keyof SearchGettersTypes]: ReturnType<SearchGettersTypes[K]>;
    };
} & {
    dispatch<K extends keyof SearchActionsTypes>(key: K, payload?: Parameters<SearchActionsTypes[K]>[1], options?: DispatchOptions): ReturnType<SearchActionsTypes[K]>;
};
