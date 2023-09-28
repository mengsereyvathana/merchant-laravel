import axios, { AxiosInstance, AxiosResponse, CreateAxiosDefaults, InternalAxiosRequestConfig } from "axios";
import { Cookie } from "../helper/index";

export type Data = object | FormData

interface IHttpRequest {
    getAll<T = unknown>(url: string, useAuth?: boolean, body?: Data): Promise<AxiosResponse<T>>;
    get<T = unknown>(url: string, useAuth?: boolean, body?: Data): Promise<AxiosResponse<T>>;
    post<T = unknown>(url: string, useAuth?: boolean, body?: Data): Promise<AxiosResponse<T>>;
    put<T = unknown>(url: string, useAuth?: boolean, body?: Data): Promise<AxiosResponse<T>>;
    delete<T = unknown>(url: string, useAuth?: boolean, body?: Data): Promise<AxiosResponse<T>>;
}

export class Http implements IHttpRequest {
    private api: AxiosInstance;

    constructor() {
        const baseConfig: CreateAxiosDefaults = {
            baseURL: "/api",
            headers: {
                Accept: "application/json",
                "Content-type": "multipart/form-data",
            },
        }
        this.api = axios.create(baseConfig)
    }

    private getRequestInstance(useAuth: boolean) {
        this.api.interceptors.request.clear();
        if (useAuth) {
            this.api.interceptors.request.use((config: InternalAxiosRequestConfig) => {
                const access_token = Cookie.get("token");
                if (access_token) {
                    config.headers.Authorization = `Bearer ${access_token}`;
                }
                return config;
            });
        }
    }

    private async request<T>(method: "GET" | "POST" | "UPDATE" | "DELETE", url: string, useAuth = false, payload?: Data): Promise<AxiosResponse<T>> {
        this.getRequestInstance(useAuth)
        const data = payload ?? {};
        const params = payload ?? {};
        return await this.api.request<T>({ method, url, data, params });
    }

    async getAll<T>(url: string, useAuth = false, params?: Data): Promise<AxiosResponse<T>> {
        return await this.request<T>("GET", url, useAuth, params);
    }
    async get<T>(url: string, useAuth = false, params?: Data): Promise<AxiosResponse<T>> {
        return await this.request<T>("GET", url, useAuth, params);
    }
    async post<T>(url: string, useAuth = false, data?: Data): Promise<AxiosResponse<T>> {
        return await this.request<T>("POST", url, useAuth, data);
    }
    async put<T>(url: string, useAuth = false, data?: Data): Promise<AxiosResponse<T>> {
        return await this.request<T>("UPDATE", url, useAuth, data);
    }
    async delete<T>(url: string, useAuth = false, data?: Data): Promise<AxiosResponse<T>> {
        return await this.request<T>("DELETE", url, useAuth, data);
    }
}
