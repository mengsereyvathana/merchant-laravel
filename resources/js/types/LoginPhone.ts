export interface ILoginPhone {
    success: boolean;
    is_new: boolean;
    data: IData;
    token: string;
}

export interface IData {
    id: number;
    name: string;
    dob: string;
    image: null;
    phone: string;
    email: null;
    email_verified_at: null;
    scheme_id: null;
    role: null;
    verify: number;
    created_at: Date;
    updated_at: Date;
}
