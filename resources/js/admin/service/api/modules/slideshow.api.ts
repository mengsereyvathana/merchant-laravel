import { ISlideshow } from "../../../types/Slideshow";
import { GenericResponse } from "../../../types/GenericResponse";
import { Http } from "../api.service";
import { SlideshowRoute } from "../api.route";
import { Form } from './types'

interface ISlideshowService {
    getAllSlideshows(pageNumber: number): Promise<Form<ISlideshow>>;
    getSlideshow(id: number): Promise<Form<ISlideshow>>
    searchSlideshows(params: object): Promise<Form<ISlideshow>>;
    createSlideshow(form: FormData): Promise<Form<ISlideshow>>;
    editSlideshow(id: number, form: FormData): Promise<Form<GenericResponse>>;
    deleteSlideshow(id: number): Promise<Form<GenericResponse>>;
}

class SlideshowService extends Http implements ISlideshowService {
    async getAllSlideshows(pageNumber: number): Promise<Form<ISlideshow>> {
        try {
            const { data } = await this.getAll<ISlideshow>(SlideshowRoute.GET_ALL, true, { page: pageNumber });
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async getSlideshow(id: number): Promise<Form<ISlideshow>> {
        try {
            const { data } = await this.get<ISlideshow>(`${SlideshowRoute.GET}/${id}`, true);
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async searchSlideshows(params: object): Promise<Form<ISlideshow>> {
        try {
            const { data } = await this.getAll<ISlideshow>(`${SlideshowRoute.SEARCH}`, true, params);
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async createSlideshow(form: FormData): Promise<Form<ISlideshow>> {
        try {
            const { data } = await this.post<ISlideshow>(SlideshowRoute.CREATE, true, form)
            return [null, data]
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async editSlideshow(id: number, form: FormData): Promise<Form<GenericResponse>> {
        try {
            const { data } = await this.post<GenericResponse>(SlideshowRoute.EDIT + id, true, form)
            return [null, data]
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async deleteSlideshow(id: number): Promise<Form<GenericResponse>> {
        try {
            const { data } = await this.delete<GenericResponse>(SlideshowRoute.DELETE, true, { id: id })
            return [null, data]
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
}
export const slideshowService = new SlideshowService();
