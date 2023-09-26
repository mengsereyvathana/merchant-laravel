export interface IProductDetail {
    success: boolean;
    data: IProductDetailItem;
}

export interface IProductDetailItem {
    id: number;
    product_id: number;
    scheme_id: number;
    unit_price: number;
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
    category: ICategory;
}

export interface ICategory {
    id: number;
    name: string;
    image: string;
    created_at: null;
    updated_at: null;
}
