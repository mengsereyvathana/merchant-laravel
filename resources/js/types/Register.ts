export interface IRegister {
    success: boolean;
    message: string;
    data: IRegisterVailidate;
}
export interface IRegisterVailidate {
    PhoneEmail: string[];
}
