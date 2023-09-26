import { ISlider } from "@/types/Slider";
import { Http } from "../ApiDataService";
import { SliderRoute } from "../route";

export class Slider extends Http {
    async getAllSliders() {
        try {
            const response = await this.getAll<ISlider>(SliderRoute.LIST_SLIDER);
            return response.data;
        } catch (error) {
            console.log(error);
            throw error;
        }
    }
}

export const sliderService = new Slider();
