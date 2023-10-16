export interface IColor {
    success: boolean;
    data: IColorItem[];
}

export interface IColorItem {
    id: number;
    name: string;
}