import { Cookie, Crypt } from "@/services/helper/index";
import { Http } from "../api.service";
import { IAuth } from "@/types/Auth";
import { IChecker } from "@/types/Checker";
import { IUser, IUserDetail } from "@/types/UserDetail";
import { RecaptchaVerifier, getAuth, signInWithPhoneNumber, signOut, signInWithCredential, PhoneAuthProvider, AuthErrorCodes, ApplicationVerifier } from "firebase/auth";
import { ILoginPhone } from "@/types/LoginPhone";
import { IFormRegister } from "@/types/FormRegister";
import { UserRoute } from "../api.route";
import { Form } from "./types";

export type UserSendCode = Pick<IUserForm, "verificationId"> | undefined;
export type SuccessChecker = Pick<IUserForm, "success">;
export type TLoginWithPhone = Pick<IUserForm, "phoneNumber" | "pss">;
export type Auth = ReturnType<typeof getAuth>;
export type UserCredential = ReturnType<typeof signInWithCredential>;
export type FirebaseErrorCodes = typeof AuthErrorCodes;

interface IUserService {
    createRecaptchaVerifier(appVerifier: RecaptchaVerifier | null, auth: Auth): Promise<RecaptchaVerifier>;
    sendCode(phoneNumber: string, appVerifier: RecaptchaVerifier, auth: Auth): Promise<UserSendCode>;
    verifyCode(verificationId: string, code: string, auth: Auth): Promise<UserCredential>;
    login(data: IUserForm): Promise<void>;
    loginWithPhone(form: FormData): Promise<Form<ILoginPhone>>;
    logoutFirebase(auth: Auth): Promise<SuccessChecker>;
    logoutBaseToken(): Promise<Form<IChecker>>
    getUser(): Promise<Form<IUserDetail>>
    checkAuth(): Promise<Form<IChecker>>
    checkUser(phoneNumber: string): Promise<{ isNewUser: boolean; message: string; token?: string | null } | undefined>;
    registerUser(full_name: string, dob: string, default_password: string, phoneNumber: string): Promise<{ success: boolean; message: string; token: string | null } | undefined>;
}

export interface IUserForm {
    success: boolean;
    name: string;
    email: string;
    password: string;
    phoneNumber: string;
    pss: string;
    verificationId: string;
    dob: string;
    isSent: true;
}

export class UserService extends Http implements IUserService {
    async createRecaptchaVerifier(appVerifier: RecaptchaVerifier | null, auth: Auth): Promise<RecaptchaVerifier> {
        return (appVerifier = new RecaptchaVerifier(auth, "recaptcha-container", { size: "invisible" }));
    }
    async sendCode(phoneNumber: string, appVerifier: RecaptchaVerifier, auth: Auth): Promise<UserSendCode> {
        try {
            const result = await signInWithPhoneNumber(auth, phoneNumber, appVerifier as ApplicationVerifier);
            return { verificationId: result.verificationId };
        } catch (error) {
            console.log("SMS NOT SENT", error);
            throw error;
        }
    }
    async verifyCode(verificationId: string, code: string, auth: Auth): Promise<UserCredential> {
        try {
            const credential = PhoneAuthProvider.credential(verificationId, code);
            const response = await signInWithCredential(auth, credential);
            return response;
        } catch (error) {
            const FirebaseError = error as FirebaseErrorCodes;
            console.log(FirebaseError);
            throw error;
        }
    }
    async login(data: IUserForm): Promise<void> {
        try {
            const formData = new FormData();
            formData.append("PhoneEmail", data.email);
            formData.append("password", data.password);

            const response = await this.post<IAuth>(UserRoute.LOGIN, false, formData);
            if (response.data.success) {
                Cookie.set("token", response.data.token, 10);
                Cookie.set("session_id", Crypt.encrypt(JSON.stringify(response.data.user.id)), 10);
            }
        } catch (error) {
            console.log(error);
        }
    }
    async loginWithPhone(form: FormData): Promise<Form<ILoginPhone>> {
        try {
            const { data } = await this.post<ILoginPhone>(`log_with_phone`, false, form);
            return [null, data];
        } catch (error) {
            console.log(error);
            return [error as Error];
        }
    }

    async logoutFirebase(auth: Auth): Promise<SuccessChecker> {
        try {
            await signOut(auth);
            const [error, data] = await userService.logoutBaseToken();
            if (error) console.log(error)
            else {
                if (data.success) {
                    Cookie.delete("token");
                    Cookie.delete("session_id");
                    return {
                        success: true,
                    };
                }
            }
            return {
                success: false,
            };
        } catch (error) {
            console.log(error);
            throw error;
        }
    }
    async logoutBaseToken(): Promise<Form<IChecker>> {
        try {
            const { data } = await this.post<IChecker>(UserRoute.LOGOUT, true);
            return [null, data]
        } catch (error) {
            console.log(error);
            return [error as Error]
        }
    }

    async getUser(): Promise<Form<IUserDetail>> {
        try {
            const { data } = await this.get<IUserDetail>(UserRoute.GET_USER, true);
            return [null, data];
        } catch (error) {
            console.log(error);
            return [error as Error];
        }
    }

    async checkAuth(): Promise<Form<IChecker>> {
        try {
            const { data } = await this.get<IChecker>(UserRoute.GET_USER, true);
            return [null, data];
        } catch (error) {
            console.log(error);
            return [error as Error];
        }
    }

    async checkUser(phoneNumber: string): Promise<{ isNewUser: boolean; message: string; token?: string | null } | undefined> {
        try {
            const formData = new FormData();
            formData.append("phoneNumber", `+855${phoneNumber}`);
            formData.append("pss", "12345678");

            const response = await this.post<ILoginPhone>(UserRoute.LOGIN_WITH_PHONE, true, formData);
            if (response.data.success) {
                if (response.data.is_new) {
                    return {
                        isNewUser: true,
                        message: "Register",
                    };
                } else {
                    Cookie.set("token", response.data.token, 10);
                    Cookie.set("session_id", Crypt.encrypt(JSON.stringify(response.data.data.id)), 10);
                    const tokenCookie = Cookie.get("token");
                    // httpAuth.defaults.headers.common["Authorization"] = `Bearer ${response.data.token}`;
                    return {
                        isNewUser: false,
                        message: "Welcome back",
                        token: tokenCookie,
                    };
                }
            }
        } catch (error) {
            console.log(error);
        }
    }
    async registerUser(full_name: string, dob: string, default_password: string, phoneNumber: string): Promise<{ success: boolean; message: string; token: string | null } | undefined> {
        try {
            const formData = new FormData();
            formData.append("phoneNumber", `+855${phoneNumber}`);
            formData.append("name", full_name);
            formData.append("dob", dob);
            formData.append("pss", default_password);

            const response = await this.post<IFormRegister>(UserRoute.REGISTER, true, formData);

            if (response.data.success) {
                Cookie.set("token", response.data.token, 10);
                Cookie.set("session_id", Crypt.encrypt(JSON.stringify(response.data.data.id)), 10);
                // httpAuth.defaults.headers.common["Authorization"] = `Bearer ${response.data.token}`;

                return {
                    success: true,
                    message: "Successfully registered",
                    token: response.data.token,
                };
            }
        } catch (error) {
            console.log(error);
        }
    }
}

export const userService = new UserService();
