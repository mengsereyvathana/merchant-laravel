export interface IProductCategory {
    success: boolean;
    data: IProductCategoryItem[];
    category_name: string;
}

export interface IProductCategoryItem {
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
    category: ICategory;
}

export interface ICategory {
    id: number;
    name: string;
    image: string;
    created_at: null;
    updated_at: null;
}
