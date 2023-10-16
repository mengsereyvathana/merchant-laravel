import { Crypt, UserID } from "@/services/helper/index";
import { Http } from "../api.service";
import { IProduct } from "@/types/Product";
import { IProductDetail } from "@/types/ProductDetail";
import { IProductCategory } from "@/types/ProductCategory";
import { ProductRoute } from "../api.route";
import { Form } from "./types";

interface IProductService {
    getAllProducts(user_id: string): Promise<Form<IProduct>>;
    getProductDetail(user_id: string, id: string): Promise<Form<IProductDetail>>;
    getProductByCategory(user_id: string, id: string): Promise<Form<IProductCategory>>;
    searchProducts(params: object): Promise<Form<IProduct>>;
}

export class ProductService extends Http implements IProductService {
    async getAllProducts(user_id: string): Promise<Form<IProduct>> {
        try {
            const { data } = await this.getAll<IProduct>(ProductRoute.LIST_PRODUCT, true, { user_id: user_id });
            return [null, data];
        } catch (error) {
            console.error(error);
            return [error as Error]
        }
    }

    async getProductDetail(user_id: string, id: string): Promise<Form<IProductDetail>> {
        try {
            const { data } = await this.get<IProductDetail>(ProductRoute.PRODUCT_DETAIL, true, { user_id: user_id, product_id: Crypt.decrypt(id), });
            return [null, data];
        } catch (error) {
            console.error(error);
            return [error as Error]
        }
    }
    async getProductByCategory(user_id: string, id: string): Promise<Form<IProductCategory>> {
        try {
            const { data } = await this.getAll<IProductCategory>(ProductRoute.PRODUCT_CATEGORY, true, { user_id: user_id, category_id: Crypt.decrypt(id), });
            return [null, data];
        } catch (error) {
            console.log(error);
            return [error as Error]
        }
    }
    async searchProducts(params: object): Promise<Form<IProduct>> {
        try {
            try {
                const { data } = await this.getAll<IProduct>(ProductRoute.SEARCH_PRODUCT, true, params);
                return [null, data];
            } catch (error) {
                console.error(error);
                return [error as Error]
            }
        } catch (error) {
            console.log(error);
            throw error;
        }
    }
}

export const productService = new ProductService();
