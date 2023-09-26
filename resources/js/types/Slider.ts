export interface ISlider {
    success: boolean;
    data: ISliderItem[];
}

export interface ISliderItem {
    id: number;
    image: string;
    slide_order: string;
    created_at: Date;
    updated_at: Date;
}
