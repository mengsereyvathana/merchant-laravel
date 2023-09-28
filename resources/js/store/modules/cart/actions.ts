import { ActionTree } from "vuex";
import {
    CartActionsTypes,
    CartStateTypes,
    IRootState,
} from "@/store/interfaces";
import { AUTH_STORE, CART_STORE } from "@/store/constants";
import { Crypt, UserID } from "@/services/helper/index";
import Swal from "sweetalert2";
import { IPayload } from "@/types/Payload";
import { cartService } from "@/services/api/modules/cart.api";
import { store } from "@/store";

export const actions: ActionTree<CartStateTypes, IRootState> &
    CartActionsTypes = {
    async [CART_STORE.ACTIONS.GET_CART_ITEM_API]({ commit }) {
        const user_id = UserID.getUser();
        const [error, data] = await cartService.getAllCart(user_id);
        if (error) console.log(error)
        else {
            if (data.success) {
                commit(CART_STORE.MUTATIONS.SET_CART, JSON.stringify(data));
            }
        }
    },
    async [CART_STORE.ACTIONS.ADD_TO_CART](
        { commit },
        payload: IPayload<string>
    ) {
        const user_id = UserID.getUser();
        const { product_id } = payload;

        // const data = {
        //     user_id: user_id,
        //     product_id: product_id,
        // };

        const formData = new FormData();
        formData.append("user_id", user_id);
        formData.append("product_id", product_id);

        Swal.fire({
            position: "center",
            allowEscapeKey: false,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        const [error, data] = await cartService.addToCart(formData);
        if (error) console.log(error);
        else {
            if (data.success) {
                Swal.close();
                commit(CART_STORE.MUTATIONS.ADD_TO_CART, data.data);
            }
        }
    },
    async [CART_STORE.ACTIONS.MINUS_FROM_CART](
        { commit },
        payload: IPayload<string>
    ) {
        const user_id = UserID.getUser();
        const { product_id } = payload;

        const formData = new FormData();
        formData.append("user_id", user_id);
        formData.append("product_id", product_id);

        Swal.fire({
            position: "center",
            allowEscapeKey: false,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        const [error, data] = await cartService.minusFromCart(formData);
        if (error) console.log(error)
        else {
            if (data.success) {
                Swal.close();
                commit(CART_STORE.MUTATIONS.MINUS_FROM_CART, data.data);
            }
        }
    },
    async [CART_STORE.ACTIONS.DELETE_FROM_CART](
        { commit },
        payload: IPayload<string>
    ) {
        try {
            const user_id = UserID.getUser();
            const { product_id, index } = payload;

            const result = await Swal.fire({
                title: "Are you sure?",
                text: "This item will be removed from your cart",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!",
            });
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append("user_id", user_id);
                formData.append("product_id", product_id);
                formData.append("_method", "DELETE");
                Swal.fire({
                    position: "center",
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });
                const [error, data] = await cartService.deleteFromCart(formData);
                if (error) console.log(error);
                else {
                    if (data.success) {
                        Swal.close();
                        commit(CART_STORE.MUTATIONS.DELETE_FROM_CART, Number(index));
                    }
                }
            }
        } catch (error) {
            console.log(error);
        }
    },
};
