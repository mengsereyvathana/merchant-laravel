import { ICategoryItem } from "./Category";
import { IProductItem } from "./Product";

export interface IFormLogin {
    username: string;
    password: string;
}

export interface IFormCategory {
    name: string;
    description: string;
    image: File | null;
    enable: boolean;
}

export interface IFormProduct {
    c_id?: number | null;
    category?: Partial<ICategoryItem>;
    name: string;
    description: string;
    price: number | null;
    buy: number | null;
    margin: number | null;
    stock: number | null;
    ram: string;
    storage: string;
    color_id: number | null;
    enable: boolean;
    image: File | null;
}

export interface IFormProductScheme {
    product_id?: number;
    scheme_id: number | null;
    unit_price: number | null;
    products: IProductItem | null;
    enable?: boolean;
}

export interface IFormSlideshow {
    order_number?: number;
    id: number;
    title: string;
    tage: string;
    link: string;
    image: File | null;
    enable: boolean;
}