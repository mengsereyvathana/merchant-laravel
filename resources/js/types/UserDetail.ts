export interface IUserDetail {
    success: boolean;
    data: IUser;
}
export interface IUser {
    id: number;
    name: string;
    dob: string;
    image: string;
    phone: null;
    email: string;
    email_verified_at: null;
    scheme_id: number;
    role: null;
    verify: number;
    created_at: Date;
    updated_at: Date;
}
