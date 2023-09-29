import { Http } from "../api.service";
import { CartRoute } from "../api.route";
import { ICart, IUpdateCart } from "@/types/Cart";
import { Form } from "./types";

interface ICartService {
    getAllCart(user_id: string): Promise<Form<ICart>>;
    addToCart(data: FormData): Promise<Form<IUpdateCart>>;
    minusFromCart(data: FormData): Promise<Form<IUpdateCart>>;
    deleteFromCart(form: FormData): Promise<Form<IUpdateCart>>;
}

interface IUserCart {
    user_id: string;
    product_id: string;
}

export class CartService extends Http implements ICartService {
    async getAllCart(user_id: string): Promise<Form<ICart>> {
        try {
            const { data } = await this.get<ICart>(CartRoute.LIST_CART, true, { user_id: user_id });
            return [null, data];
        } catch (error) {
            console.log(error);
            return [error as Error]
        }
    }
    async addToCart(form: FormData): Promise<Form<IUpdateCart>> {
        try {
            const { data } = await this.post<IUpdateCart>(CartRoute.ADD_TO_CART, true, form);
            return [null, data];
        } catch (error) {
            console.log(error);
            return [error as Error];
        }
    }
    async minusFromCart(form: FormData): Promise<Form<IUpdateCart>> {
        try {
            const { data } = await this.post<IUpdateCart>(CartRoute.SUB_TO_CART, true, form);
            return [null, data];
        } catch (error) {
            console.log(error);
            return [error as Error];
        }
    }
    async deleteFromCart(form: FormData): Promise<Form<IUpdateCart>> {
        try {
            const { data } = await this.post<IUpdateCart>(CartRoute.DELETE_CART, true, form);
            return [null, data];
        } catch (error) {
            console.log(error);
            return [error as Error];
        }
    }
}
export const cartService = new CartService();
