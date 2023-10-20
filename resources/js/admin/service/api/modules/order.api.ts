import { GenericResponse } from "../../../types/GenericResponse";
import { Http } from "../api.service";
import { OrderRoute, SlideshowRoute } from "../api.route";
import { Form } from './types'
import { IOrder } from "../../../types/Order";

interface IOrderService {
    getAllOrders(pageNumber: number): Promise<Form<IOrder>>;
    // createOrder(form: FormData): Promise<Form<ISlideshow>>;
    // editOrder(form: FormData): Promise<Form<ISlideshow>>;
    deleteOrder(id: number): Promise<Form<GenericResponse>>;
}

class OrderService extends Http implements IOrderService {
    async getAllOrders(pageNumber: number): Promise<Form<IOrder>> {
        try {
            const { data } = await this.getAll<IOrder>(OrderRoute.GET_ALL, true, { page: pageNumber });
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async searchOrders(params: object): Promise<Form<IOrder>> {
        try {
            const { data } = await this.getAll<IOrder>(`${OrderRoute.SEARCH}`, true, params);
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    // async createOrder(form: FormData): Promise<Form<ISlideshow>> {
    //     try {
    //         const { data } = await this.post<ISlideshow>(SlideshowRoute.CREATE, true, form)
    //         return [null, data]
    //     } catch (error) {
    //         console.error(error)
    //         return [error as Error];
    //     }
    // }
    // async editOrder(form: FormData): Promise<Form<ISlideshow>> {
    //     try {
    //         const { data } = await this.post<ISlideshow>(SlideshowRoute.EDIT, true, form)
    //         return [null, data]
    //     } catch (error) {
    //         console.error(error)
    //         return [error as Error];
    //     }
    // }
    async deleteOrder(id: number): Promise<Form<GenericResponse>> {
        try {
            const { data } = await this.delete<GenericResponse>(SlideshowRoute.DELETE, true, { id: id })
            return [null, data]
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
}

export const orderService = new OrderService();
