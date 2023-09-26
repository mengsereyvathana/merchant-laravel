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
import { User } from "@/services/api/modules/user.api";

export const actions: ActionTree<CartStateTypes, IRootState> &
    CartActionsTypes = {
    async [CART_STORE.ACTIONS.GET_CART_ITEM_API]({ commit }) {
        const user_id  = UserID.getUser();
        const response = await cartService.getAllCart(user_id);
        if (response.success) {
            commit(CART_STORE.MUTATIONS.SET_CART, JSON.stringify(response));
        }
    },
    async [CART_STORE.ACTIONS.ADD_TO_CART](
        { commit },
        payload: IPayload<string>
    ) {
        const user_id  = UserID.getUser();
        const { product_id } = payload;

        const data = {
            user_id: user_id,
            product_id: product_id,
        };

        Swal.fire({
            position: "center",
            allowEscapeKey: false,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        const response = await cartService.addToCart(data);
        if (response.success) {
            Swal.close();
            commit(CART_STORE.MUTATIONS.ADD_TO_CART, response.data);
        }
    },
    async [CART_STORE.ACTIONS.MINUS_FROM_CART](
        { commit },
        payload: IPayload<string>
    ) {
        const user_id  = UserID.getUser();
        const { product_id } = payload;

        const data = {
            user_id: user_id,
            product_id: product_id,
        };
        Swal.fire({
            position: "center",
            allowEscapeKey: false,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            },
        });

        const response = await cartService.minusFromCart(data);
        if (response.success) {
            Swal.close();
            commit(CART_STORE.MUTATIONS.MINUS_FROM_CART, response.data);
        }
    },
    async [CART_STORE.ACTIONS.DELETE_FROM_CART](
        { commit },
        payload: IPayload<string>
    ) {
        try {
            const user_id  = UserID.getUser();
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
                const data = {
                    user_id: user_id,
                    product_id: product_id,
                };
                Swal.fire({
                    position: "center",
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    didOpen: () => {
                        Swal.showLoading();
                    },
                });
                const response = await cartService.deleteFromCart(data);
                if (response.success) {
                    Swal.close();
                    commit(
                        CART_STORE.MUTATIONS.DELETE_FROM_CART,
                        Number(index)
                    );
                }
            }
        } catch (error) {
            console.log(error);
            Swal.fire({
                toast: true,
                position: "center",
                showClass: {
                    icon: "animated heartBeat delay-1s",
                },
                icon: "info",
                text: "Unauthorized",
                showConfirmButton: false,
                timer: 1000,
            });
        }
    },
};
