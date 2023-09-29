import { ISlider } from "@/types/Slider";
import { Http } from "../api.service";
import { SliderRoute } from "../api.route";
import { Form } from "./types";

interface ISlideshowService {
    getAllSliders(): Promise<Form<ISlider>>;
}
export class SlideshowService extends Http implements ISlideshowService {
    async getAllSliders(): Promise<Form<ISlider>> {
        try {
            const { data } = await this.getAll<ISlider>(SliderRoute.LIST_SLIDER);
            return [null, data];
        } catch (error) {
            console.log(error);
            return [error as Error];
        }
    }
}

export const sliderService = new SlideshowService();
