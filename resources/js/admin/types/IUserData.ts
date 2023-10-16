export interface IUserData {
    value: IUserDataItem;
    name: any;
    success: boolean;
    data: IUserDataItem;
}

export interface IUserDataItem {
    id: number;
    name: string;
    dob: string;
    image: string;
    phone: string;
    email: string;
    email_verified_at: null;
    scheme_id: number;
    role: number;
    verify: number;
    created_at: Date;
    updated_at: Date;
}
