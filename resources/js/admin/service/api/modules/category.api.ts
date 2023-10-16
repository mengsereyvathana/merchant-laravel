import { GenericResponse } from "../../../types/GenericResponse";
import { Http } from "../api.service";
import { CategoryRoute } from "../api.route";
import { Form } from './types'
import { ICategory } from "../../../types/Category";

interface ICategoryService {
    getAllCategories(pageNumber: number): Promise<Form<ICategory>>;
    getCategory(id: number): Promise<Form<ICategory>>;
    searchCategories(params: object): Promise<Form<ICategory>>;
    createCategory(form: FormData): Promise<Form<ICategory>>;
    editCategory(id: number, form: FormData): Promise<Form<GenericResponse>>;
    deleteCategory(id: number): Promise<Form<GenericResponse>>;
}

class CategoryService extends Http implements ICategoryService {
    async getAllCategories(pageNumber: number): Promise<Form<ICategory>> {
        try {
            const { data } = await this.getAll<ICategory>(CategoryRoute.GET_ALL, true, { page: pageNumber });
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async getCategory(id: number): Promise<Form<ICategory>> {
        try {
            const { data } = await this.get<ICategory>(CategoryRoute.GET + id, true);
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async searchCategories(params: object): Promise<Form<ICategory>> {
        try {
            const { data } = await this.getAll<ICategory>(`${CategoryRoute.SEARCH}`, true, params);
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async createCategory(form: FormData): Promise<Form<ICategory>> {
        try {
            const { data } = await this.post<ICategory>(CategoryRoute.CREATE, true, form)
            return [null, data]
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async editCategory(id: number, form: FormData): Promise<Form<GenericResponse>> {
        try {
            const { data } = await this.post<GenericResponse>(CategoryRoute.EDIT + id, true, form)
            return [null, data]
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async deleteCategory(id: number): Promise<Form<GenericResponse>> {
        try {
            const { data } = await this.delete<GenericResponse>(CategoryRoute.DELETE + id, true)
            return [null, data]
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
}

export const categoryService = new CategoryService();
