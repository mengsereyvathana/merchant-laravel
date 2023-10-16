import { Http } from "../api.service";
import { IProduct } from "../../../types/Product";
import { ProductRoute } from "../api.route";
import { Form } from './types'
import { GenericResponse } from "../../../types/GenericResponse";

interface IProductService {
    getAllProducts(pageNumber: number): Promise<Form<IProduct>>;
    getProduct(id: number): Promise<Form<IProduct>>;
    createProduct(form: FormData): Promise<Form<IProduct>>;
    searchProducts(params: object): Promise<Form<IProduct>>
    editProduct(id: number, form: FormData): Promise<Form<GenericResponse>>;
    deleteProduct(id: number): Promise<Form<GenericResponse>>;
}

class ProductService extends Http implements IProductService {
    async getAllProducts(pageNumber: number): Promise<Form<IProduct>> {
        try {
            const { data } = await this.getAll<IProduct>(`${ProductRoute.GET_ALL}`, true, { page: pageNumber });
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async getProduct(id: number): Promise<Form<IProduct>> {
        try {
            const { data } = await this.getAll<IProduct>(`${ProductRoute.GET}/${id}`, true);
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async searchProducts(params: object): Promise<Form<IProduct>> {
        try {
            const { data } = await this.getAll<IProduct>(`${ProductRoute.SEARCH}`, true, params);
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async createProduct(form: FormData): Promise<Form<IProduct>> {
        try {
            const { data } = await this.post<IProduct>(`${ProductRoute.CREATE}`, true, form);
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async editProduct(id: number, form: FormData): Promise<Form<GenericResponse>> {
        try {
            const { data } = await this.post<GenericResponse>(ProductRoute.EDIT + id, true, form);
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async deleteProduct(id: number): Promise<Form<GenericResponse>> {
        try {
            const { data } = await this.delete<GenericResponse>(ProductRoute.DELETE + id, true);
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }

}

export const productService = new ProductService();
