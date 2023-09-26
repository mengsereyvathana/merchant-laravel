import { UserID } from "@/services/helper/index";
import { Http } from "../ApiDataService";
import { CartRoute } from "../route";
import { ICart, IUpdateCart } from "@/types/Cart";
import { IAddOrderDetail } from "@/types/OrderDetail";
import { IAddOrder } from "@/types/Order";

interface CartData {
    order_id: number;
    product_id: number | undefined;
    qty: number;
    unit_price: number;
    discount: number;
}

interface IDataOrder {
    user_id: string;
    address_id: string;
    pay_by: string;
}

interface IUserCart {
    user_id: string;
    product_id: string;
}

export class Cart extends Http {
    async getAllCart(user_id: string) {
        try {
            const response = await this.get<ICart>(
                CartRoute.LIST_CART,
                { params: { user_id } },
                true
            );
            return response.data;
        } catch (error) {
            console.log(error);
            throw error;
        }
    }
    async addToCart(data: IUserCart) {
        try {
            const formData = new FormData();
            formData.append("user_id", data.user_id);
            formData.append("product_id", data.product_id);
            const response = await this.post<IUpdateCart>(
                CartRoute.ADD_TO_CART,
                formData,
                true
            );
            return response.data;
        } catch (error) {
            console.log(error);
            throw error;
        }
    }
    async minusFromCart(data: IUserCart) {
        try {
            const formData = new FormData();
            formData.append("user_id", data.user_id);
            formData.append("product_id", data.product_id);
            const response = await this.post<IUpdateCart>(
                CartRoute.SUB_TO_CART,
                formData,
                true
            );
            return response.data;
        } catch (error) {
            console.log(error);
            throw error;
        }
    }
    async deleteFromCart(data: IUserCart) {
        try {
            const formData = new FormData();
            formData.append("user_id", data.user_id);
            formData.append("product_id", data.product_id);
            formData.append("_method", "DELETE");
            const response = await this.post<IUpdateCart>(
                CartRoute.DELETE_CART,
                formData,
                true
            );
            return response.data;
        } catch (error) {
            console.log(error);
            throw error;
        }
    }
    async addOrder(data: IDataOrder) {
        try {
            const formDataOrder = new FormData();
            formDataOrder.append("user_id", data.user_id);
            formDataOrder.append("address_id", data.address_id);
            formDataOrder.append("pay_by", data.pay_by);
            const response = await this.post<IAddOrder>(
                `order`,
                formDataOrder,
                true
            );
            return response.data;
        } catch (error) {
            console.log(error);
            throw error;
        }
    }
    async addOrderDetail(data: CartData[]) {
        try {
            const response = await this.post<IAddOrderDetail>(
                `order_detail`,
                data,
                true
            );
            return response.data;
        } catch (error) {
            console.log(error);
            throw error;
        }
    }
}
export const cartService = new Cart();
