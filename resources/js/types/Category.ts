export interface ICategory {
    success: boolean;
    data: ICategoryItem[];
}

export interface ICategoryItem {
    id: number;
    name: string;
    image: string;
    created_at: null;
    updated_at: null;
}
