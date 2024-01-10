export interface IAdminLogin {
    success: boolean;
    message: string;
    data: ILoginMessage,
    user: IUser;
    token: string;
}

export interface IUser {
    id: number;
    name: string;
    dob: null;
    image: null;
    phone: null;
    email: null;
    email_verified_at: null;
    scheme_id: null;
    role: number;
    verify: number;
    created_at: null;
    updated_at: null;
}

export interface ILoginMessage {
    name: Array<string[]>;
    password: Array<string[]>;
}
