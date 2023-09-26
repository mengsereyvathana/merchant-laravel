export interface IAddOrderDetail {
    success: boolean;
    data: IOrderDetailItem[];
}

export interface IOrderDetailItem {
    id: number;
    order_id: number;
    product_id: string;
    qty: number;
    unit_price: number;
    discount: number;
    total: number;
    created_at: Date;
    updated_at: Date;
}
