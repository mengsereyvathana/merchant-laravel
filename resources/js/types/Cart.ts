export interface ICart {
    success: boolean;
    data: ICartItem[];
    amount: number;
    amount_qty: number;
}

export interface ICartItem {
    id: number;
    user_id?: number;
    product_id?: number;
    qty: number;
    unit_price: number;
    total: number;
    created_at?: Date;
    updated_at?: Date;
    products?: IProducts;
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

export interface IUpdateCart {
    success: boolean;
    data: IData;
}

export interface IData {
    id: number;
    user_id: number;
    product_id: number;
    qty: number;
    unit_price: number;
    total: number;
    created_at: Date;
    updated_at: Date;
}
