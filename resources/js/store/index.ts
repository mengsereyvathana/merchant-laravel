import { createStore } from "vuex";
import { IRootState } from "@/store/interfaces";
import { AuthStoreModuleTypes } from "./modules/auth/types";
import { CartStoreModuleTypes } from "./modules/cart/types";
import { RootStoreModuleTypes } from "./modules/root/types";
import { SearchStoreModuleTypes } from "./modules/search/types";

import root from "./modules/root";
export const store = createStore<IRootState>(root);

type StoreModules = {
    authModule: AuthStoreModuleTypes;
    cartModule: CartStoreModuleTypes;
    searchModule: SearchStoreModuleTypes;
    root: RootStoreModuleTypes;
};

export type Store = AuthStoreModuleTypes<Pick<StoreModules, "authModule">> & CartStoreModuleTypes<Pick<StoreModules, "cartModule">> & SearchStoreModuleTypes<Pick<StoreModules, "searchModule">> & RootStoreModuleTypes<Pick<StoreModules, "root">>;
