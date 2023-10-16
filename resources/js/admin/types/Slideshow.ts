export interface ISlideshow {
    success: boolean;
    data: ISlideshowItem[] | ISlideshowItem;
    per_page: number;
    sum_page: number;
    total_item: number;
    total_page: number;
}

export interface ISlideshowItem {
    id: number;
    image: string;
    title: string;
    tage: string;
    link: string;
    action: string;
    slide_order: number;
    created_at?: Date;
    updated_at?: Date;
}
