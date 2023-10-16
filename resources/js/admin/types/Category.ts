export interface ICategory {
    success: boolean;
    data: ICategoryItem[] | ICategoryItem;
    per_page: number;
    sum_page: number;
    total_item: number;
    total_page: number;
}

export interface ICategoryItem {
    id: number;
    name: string;
    description: string;
    image: string;
    action: string;
    created_at: Date | null;
    updated_at: Date | null;
}
