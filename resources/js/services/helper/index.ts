import CryptoJS from "crypto-js";
import { API_URL } from "@/config/api.config";
import { store } from "@/store";
import { AUTH_STORE } from "@/store/constants";

export interface CookieTypes {
    token: string;
    session_id: string;
    onVerification: string;
}
const CookieChecker: CookieTypes = {
    token: "",
    session_id: "",
    onVerification: "",
};
export type CookieType = keyof typeof CookieChecker;

export class UserID {
    static getUser() {
        return Crypt.decrypt(store.getters[AUTH_STORE.GETTERS.GET_USER_ID] as string) as string
    }
}

// export class httpHeaders {
//     static getToken() {
//         return {
//             token: Cookie.get("token"),
//         };
//     }
// }

export class Cookie {
    static set(name: CookieType, value: string, day: number): void {
        const token = value;
        let expires = "";

        const date = new Date();
        if (value === "onVerification") {
            date.setTime(date.getTime() + day * 1000);
        } else {
            date.setTime(date.getTime() + day * 24 * 60 * 60 * 1000);
        }
        expires = "; expires=" + date.toUTCString();

        document.cookie = name + "=" + token + expires + "; path=/";
    }
    static get(value: CookieType): string | null {
        const cookies = document.cookie.split(";");

        for (let i = 0; i < cookies.length; i++) {
            const cookie = cookies[i].trim();

            if (cookie.indexOf(value + "=") === 0) {
                return cookie.substring(value.length + 1);
            }
        }
        return null;
    }
    static delete(name: string): void {
        document.cookie = `${name}=; expires=${new Date(0).toUTCString()}`;
    }
}

export class Crypt {
    static encrypt(value: string): string {
        const encrypted = CryptoJS.AES.encrypt(value, "secret key")
            .toString()
            .replace(/\+/g, "-")
            .replace(/\//g, "_")
            .replace(/=/g, "");
        return encrypted;
    }
    static decrypt(value: string): string | null {
        if (value) {
            const encrypted = value.replace(/-/g, "+").replace(/_/g, "/");
            if (encrypted) {
                const decryptedItemsString = CryptoJS.AES.decrypt(
                    encrypted,
                    "secret key"
                ).toString(CryptoJS.enc.Utf8);
                return decryptedItemsString;
            } else {
                return null;
            }
        }
        return null;
    }
}

export class Upload {
    static icon(value: string): string {
        return "/icons/" + value;
    }
    static image(value: string | undefined): string {
        return value as string;
    }
}
