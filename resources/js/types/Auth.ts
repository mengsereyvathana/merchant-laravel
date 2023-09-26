export interface IAuth extends Error {
    success: boolean;
    is_new: boolean;
    user: IUser;
    token: string;
}

export interface IUser {
    id: number;
    name: string;
    dob: null;
    image: null;
    phone: null;
    email: string;
    email_verified_at: null;
    scheme_id: number;
    role: null;
    verify: number;
    created_at: Date;
    updated_at: Date;
    scheme: IScheme;
}

export interface IScheme {
    id: number;
    name: string;
    type: string;
    scheme_price: number;
    created_at: Date;
    updated_at: Date;
}

//login false
export interface Error {
    validation_error: boolean;
    message: string;
    data: IData;
}

export interface IData {
    PhoneEmail: string[];
    password: string[];
}
