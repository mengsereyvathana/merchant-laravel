import axios, { AxiosInstance } from "axios";
import { API_URL } from "@/config/api.config";

export const baseConfig = {
    baseURL: "/api",
    headers: {
        "Content-type": "application/json",
    },
};

export const authConfig = {
    ...baseConfig,
    headers: {
        ...baseConfig.headers,
        Accept: "application/json",
    },
};

export const http: AxiosInstance = axios.create(baseConfig);

export const httpAuth: AxiosInstance = axios.create(authConfig);

export class BaseService {
    protected readonly api: AxiosInstance;
    protected readonly apiAuth: AxiosInstance;
    constructor() {
        this.api = http;
        this.apiAuth = httpAuth;
    }
}
