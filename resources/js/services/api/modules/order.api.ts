import { UserID } from "@/services/helper/index";
import { Http } from "../ApiDataService";
import { IOrder } from "@/types/Order";
import { OrderRoute } from "../route";

export class Order extends Http {
    async getAllOrders(user_id: string) {
        try {
            const { data } = await this.get<IOrder>(OrderRoute.LIST_ORDERED, { params: { user_id: user_id } }, true);
            return [data];
        } catch (error) {
            console.error(error)
            throw new Error("Error is " + error)
        }
    }
}
export const orderService = new Order();
