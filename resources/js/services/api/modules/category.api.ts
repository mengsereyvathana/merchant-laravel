import { Http } from "../api.service";
import { ICategory } from "@/types/Category";
import { CategoryRoute } from "../api.route";
import { Form } from "./types";

interface ICategoryService {
    getAllCategories(): Promise<Form<ICategory>>;
}

export class CategoryService extends Http implements ICategoryService {
    async getAllCategories(): Promise<Form<ICategory>> {
        try {
            const { data } = await this.getAll<ICategory>(CategoryRoute.LIST_CATEGORY);
            return [null, data];
        } catch (error) {
            console.log(error);
            return [error as Error];
        }
    }
}

export const categoryService = new CategoryService();
