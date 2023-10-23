export interface IOrder {
    success: boolean;
    data: Array<IOrderItem[]>;
    per_page: number;
    sum_page: number;
    total_invocie: number;
    total_page: number;
    invoice: number[];
    invoice_date: string[];
    total: number[];
}

export interface IOrderItem {
    id: number;
    order_id: number;
    product_id: string;
    qty: number;
    unit_price: number;
    discount: number;
    total: number;
    created_at: Date;
    updated_at: Date;
    order: Order;
    product: Product;
}

export interface Order {
    id: number;
    invoice: number;
    user_id: string;
    address_id: string;
    pay_by: string;
    status: string;
    created_at: Date;
    updated_at: Date;
}

export interface Product {
    id: number;
    name: string;
    image: string;
    color: string;
    description: string;
    price: number;
    category_id: null;
    ram: string;
    storage: string;
    buy: number;
    margin: number;
    stock: number;
    action: string;
    created_at: Date;
    updated_at: Date;
}
