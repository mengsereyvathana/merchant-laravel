import { Http } from "../ApiDataService";
import { ICategory } from "@/types/Category";
import { CategoryRoute } from "../route";

export class Category {
    protected readonly http: Http;
    constructor() {
        this.http = new Http();
    }
    async getAll() {
        try {
            const response = await this.http.getAll<ICategory>(CategoryRoute.LIST_CATEGORY);
            return response.data;
        } catch (error) {
            console.log(error);
            throw error;
        }
    }
}

export const categoryService = new Category();
