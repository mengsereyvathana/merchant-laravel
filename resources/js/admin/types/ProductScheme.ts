export interface IProductScheme {
    success: boolean;
    message: string;
    data: IProductSchemeItem | IProductSchemeItem[];
    per_page: number;
    sum_page: number;
    total_item: number;
    total_page: number;
}

export interface IProductSchemeItem {
    id: number;
    product_id: number;
    scheme_id: number;
    unit_price: number;
    margin: number;
    created_at: Date;
    updated_at: Date;
    action: string;
    products: Products | null;
}

export interface Products {
    id: number;
    name: string;
    image: string;
    color: string;
    description: string;
    price: number;
    category_id: number;
    ram: string;
    storage: string;
    buy: number;
    margin: number;
    stock: number;
    action: string;
    created_at: Date;
    updated_at: Date;
    category: null;
}
