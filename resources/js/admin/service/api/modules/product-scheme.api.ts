import { Http } from "../api.service";
import { ProductSchemeRoute } from "../api.route";
import { Form } from './types'
import { GenericResponse } from "../../../types/GenericResponse";
import { IProductScheme } from "../../../types/ProductScheme";

interface IProductSchemeService {
    getAllProductScheme(pageNumber: number): Promise<Form<IProductScheme>>;
    getProductScheme(id: number): Promise<Form<IProductScheme>>;
    searchProducts(params: object): Promise<Form<IProductScheme>>;
    createProductScheme(form: FormData): Promise<Form<IProductScheme>>;
    editProductScheme(id: number, form: FormData): Promise<Form<GenericResponse>>;
    deleteProductScheme(id: number): Promise<Form<GenericResponse>>;
}

class ProductSchemeService extends Http implements IProductSchemeService {
    async getAllProductScheme(pageNumber: number): Promise<Form<IProductScheme>> {
        try {
            const { data } = await this.getAll<IProductScheme>(ProductSchemeRoute.GET_ALL, true, { page: pageNumber });
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async getProductScheme(id: number): Promise<Form<IProductScheme>> {
        try {
            const { data } = await this.getAll<IProductScheme>(`${ProductSchemeRoute.GET}/${id}`, true);
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async searchProducts(params: object): Promise<Form<IProductScheme>> {
        try {
            const { data } = await this.getAll<IProductScheme>(`${ProductSchemeRoute.SEARCH}`, true, params);
            console.log(data)
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async createProductScheme(form: FormData): Promise<Form<IProductScheme>> {
        try {
            const { data } = await this.post<IProductScheme>(`${ProductSchemeRoute.CREATE}`, true, form);
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async editProductScheme(id: number, form: FormData): Promise<Form<GenericResponse>> {
        try {
            const { data } = await this.post<GenericResponse>(`${ProductSchemeRoute.EDIT}/${id}`, true, form);
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async deleteProductScheme(id: number): Promise<Form<GenericResponse>> {
        try {
            const { data } = await this.delete<GenericResponse>(`${ProductSchemeRoute.DELETE}/${id}`, true);
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
}

export const productSchemeService = new ProductSchemeService();
