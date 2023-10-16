export interface IProduct {
    success: boolean;
    data: IProductItem[] | IProductItem;
    sum_page: number;
    total_item: number;
    per_page: number;
    total_page: number;
}

export interface IProductItem {
    id: number;
    name: string;
    image: string;
    color: string;
    description: string;
    price: number;
    category_id: number | null;
    ram: string;
    storage: string;
    buy: number;
    margin: number;
    stock: number;
    action: string;
    created_at: Date;
    updated_at: Date;
    category: Category | null;
}

export interface Category {
    id: number;
    name: string;
    des: null;
    image: string;
    created_at: Date | null;
    updated_at: Date | null;
}
