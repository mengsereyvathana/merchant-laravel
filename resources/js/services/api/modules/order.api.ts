import { UserID } from "@/services/helper/index";
import { Http } from "../api.service";
import { IAddOrder, IOrder } from "@/types/Order";
import { OrderRoute } from "../api.route";
import { Form } from "./types";
import { IAddOrderDetail } from "@/types/OrderDetail";

interface IOrderService {
    getAllOrders(user_id: string, pageNumber: number): Promise<Form<IOrder>>;
    addOrder(form: FormData): Promise<Form<IAddOrder>>;
    addOrderDetail(form: CartData[]): Promise<Form<IAddOrderDetail>>;
}
interface CartData {
    order_id: number;
    product_id: number | undefined;
    qty: number;
    unit_price: number;
    discount: number;
}

export class OrderService extends Http implements IOrderService {
    async getAllOrders(user_id: string, pageNumber: number): Promise<Form<IOrder>> {
        try {
            const { data } = await this.getAll<IOrder>(OrderRoute.LIST_ORDERED, true, { user_id: user_id, page: pageNumber });
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async addOrder(form: FormData): Promise<Form<IAddOrder>> {
        try {
            const { data } = await this.post<IAddOrder>(`order`, true, form);
            return [null, data];
        } catch (error) {
            console.log(error);
            return [error as Error]
        }
    }
    async addOrderDetail(form: CartData[]): Promise<Form<IAddOrderDetail>> {
        try {
            const { data } = await this.post<IAddOrderDetail>(`order_detail`, true, form);
            return [null, data];
        } catch (error) {
            console.log(error);
            return [error as Error]
        }
    }
}
export const orderService = new OrderService();
