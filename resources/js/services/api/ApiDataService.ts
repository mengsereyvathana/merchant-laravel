import ResponseData from "./ResponseData";
import Data from "./Data";
import { AxiosInstance, AxiosResponse } from "axios";
import { BaseService } from "./http.common";

type PromiseData<T> = Promise<ResponseData<T>>;

interface IHttpRequest {
    getAll<T>(url: string, payload?: Data, useAuth?: boolean): Promise<AxiosResponse<PromiseData<T>>>;
    get<T>(url: string, payload?: Data, useAuth?: boolean): Promise<AxiosResponse<PromiseData<T>>>;
    post<T>(url: string, payload?: Data, useAuth?: boolean): Promise<AxiosResponse<PromiseData<T>>>;
    put<T>(url: string, payload?: Data, useAuth?: boolean): Promise<AxiosResponse<PromiseData<T>>>;
    delete<T>(url: string, payload?: Data, useAuth?: boolean): Promise<AxiosResponse<PromiseData<T>>>;
}

export class Http extends BaseService implements IHttpRequest {
    private getRequestInstance(useAuth: boolean): AxiosInstance {
        return useAuth ? this.apiAuth : this.api;
    }
    private async request<T>(method: "get" | "post" | "put" | "delete", url: string, data?: Data, params?: object, useAuth?: boolean) {
        const axiosInstance = this.getRequestInstance(useAuth as boolean);
        return axiosInstance.request<T>({ method, url, data, params });
    }
    async getAll<T>(url: string, payload?: Data, useAuth?: boolean) {
        return await this.request<T>("get", url, payload, payload?.params, useAuth);
    }
    async get<T>(url: string, payload?: Data, useAuth?: boolean) {
        return await this.request<T>("get", url, payload, payload?.params, useAuth);
    }
    async post<T>(url: string, payload?: Data, useAuth?: boolean) {
        return await this.request<T>("post", url, payload, payload?.params, useAuth);
    }
    async put<T>(url: string, payload?: Data, useAuth?: boolean) {
        return await this.request<T>("put", url, payload, payload?.params, useAuth);
    }
    async delete<T>(url: string, payload?: Data, useAuth?: boolean) {
        return await this.request<T>("delete", url, payload, payload?.params, useAuth);
    }
}
