import { API_URL } from "@/config/api.config";
export class Upload {
    static icon(value: string): string {
        return "/icons/" + value;
    }
    static image(value: string | undefined): string {
        return value ?? '';
    }
}