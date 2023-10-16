import { Http } from "../api.service";
import { AuthRoute } from "../api.route";
import { Form } from './types'
import { IAdminLogin } from "../../../types/AdminAuth";
import { IUserData } from "../../../types/IUserData";
import { GenericResponse } from "../../../types/GenericResponse";

interface IAdminAuthService {
    login(form: FormData): Promise<Form<IAdminLogin>>;
    logout(): Promise<Form<GenericResponse>>;
    getUser(): Promise<Form<IUserData>>;
    isAuthenticated(): Promise<boolean>;
}

class AdminAuthService extends Http implements IAdminAuthService {
    async login(form: FormData): Promise<Form<IAdminLogin>> {
        try {
            const { data } = await this.post<IAdminLogin>(AuthRoute.LOGIN, false, form);
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async logout(): Promise<Form<GenericResponse>> {
        try {
            const { data } = await this.post<GenericResponse>(AuthRoute.LOGOUT, true);
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async getUser(): Promise<Form<IUserData>> {
        try {
            const { data } = await this.get<IUserData>(AuthRoute.USER, true);
            return [null, data];
        } catch (error) {
            console.error(error)
            return [error as Error];
        }
    }
    async isAuthenticated(): Promise<boolean> {
        const token = sessionStorage.getItem("adminToken");
        if (token) {
            return true;
        }
        return false;
    }
}

export const adminAuthService = new AdminAuthService();
