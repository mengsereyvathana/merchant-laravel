import { UserID } from "@/services/helper/index";
import { Http } from "../api.service";
import { IOrder } from "@/types/Order";
import { OrderRoute } from "../route";
import { Form } from "./types";

interface IOrderService {
    getAllOrders(user_id: string): Promise<Form<IOrder>>;
}

export class OrderService extends Http implements IOrderService {
    async getAllOrders(user_id: string): Promise<Form<IOrder>> {
        try {
            const { data } = await this.get<IOrder>(OrderRoute.LIST_ORDERED, true, { user_id: user_id });
            return [null, data];
        } catch (error) {
            console.error(error)
            throw new Error("Error is " + error)
        }
    }
}
export const orderService = new OrderService();
