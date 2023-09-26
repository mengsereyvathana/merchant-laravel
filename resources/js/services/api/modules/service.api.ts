import { Http } from "../ApiDataService";

export class Api<T> {
    protected readonly http: Http;
    constructor() {
        this.http = new Http();
    }
    async getAll(url: string) {
        try {
            const response = await this.http.getAll<T>(url);
            return response.data;
        } catch (error) {
            console.log(error);
            throw error;
        }
    }
}
