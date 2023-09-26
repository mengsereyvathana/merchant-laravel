import { Crypt, UserID } from "@/services/helper/index";
import { Http } from "../ApiDataService";
import { IProduct } from "@/types/Product";
import { IProductDetail } from "@/types/ProductDetail";
import { IProductCategory } from "@/types/ProductCategory";
import { ProductRoute } from "../route";
import { DataService } from "@/types/DataService";

export class Product extends Http {
    async getAllProduct(user_id: string) {
        try {
            const response = await this.getAll<IProduct>(
                ProductRoute.LIST_PRODUCT,
                {
                    params: { user_id: user_id },
                },
                true
            );
            return response.data;
        } catch (error) {
            console.error(error);
            throw error;
        }
    }

    async getProductDetail(user_id: string, id: string) {
        try {
            const params = {
                user_id: user_id,
                product_id: Crypt.decrypt(id),
            };
            const response = await this.get<IProductDetail>(
                ProductRoute.PRODUCT_DETAIL,
                {
                    params,
                },
                true
            );
            return response.data;
        } catch (error) {
            console.error(error);
            throw error;
        }
    }
    async getProductByCategory(
        user_id: string,
        id: string
    ): Promise<IProductCategory> {
        try {
            const params = {
                user_id: user_id,
                category_id: Crypt.decrypt(id),
            };
            const response = await this.getAll<IProductCategory>(
                ProductRoute.PRODUCT_CATEGORY,
                { params },
                true
            );
            return response.data;
        } catch (error) {
            console.log(error);
            throw error;
        }
    }
    async searchProduct(data: any) {
        try {
            console.log(data);
            // const response = await this.get<IProduct>(
            //     ProductRoute.SEARCH_PRODUCT,
            //     {
            //         data,
            //     }
            // );
            // return response.data;
        } catch (error) {
            console.log(error);
            throw error;
        }
    }
}

export const productService = new Product();
