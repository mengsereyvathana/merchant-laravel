export interface IProduct {
    success: boolean;
    data: IProductItem[];
    sum_page: number;
    total_page: number;
}

export interface IProductItem {
    id: number;
    product_id: number;
    scheme_id: number;
    unit_price: number;
    margin: number;
    products: IProducts;
}

export interface IProducts {
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
}
