<script setup lang="ts">
import { onMounted, ref, reactive } from "vue";
import { RouterLink, useRouter } from "vue-router";
import { Crypt, Cookie, Upload } from '@/services/helper/index'
import { Http } from "@/services/api/ApiDataService";
import { IAuth } from '@/types/Auth';
import { AUTH_STORE } from "@/store/constants";
import { useStore } from "@/use/useStore";
import { userService } from "@/services/api/modules/user.api";
import Swal from "sweetalert2";

const store = useStore();
const input1 = ref<HTMLInputElement | null>(null);
const input2 = ref<HTMLInputElement | null>(null);

let message = ref<{ email_validate: string, password_validate: string[] }>({
    email_validate: '',
    password_validate: []
})

const router = useRouter();
const token = ref<string | null>('');

interface Form {
    email: string,
    password: string,
    remember: boolean,
}

let form: Form = reactive({
    email: "",
    password: "",
    remember: false,
});


const checkAuth = async () => {
    token.value = store.getters[AUTH_STORE.GETTERS.GET_TOKEN]
    const response = await userService.checkAuth(token.value);
    if (response.success) {
        router.replace("/")
    } else {
        router.replace("/phone_login")
    }
}

onMounted(() => {
    checkAuth();
    input1.value?.focus();
});

const login = async () => {
    // message.value.email_validate = "";
    // message.value.password_validate = [];
    // if (form.email == "" || form.password == "")
    //     return Swal.fire({
    //         toast: true,
    //         position: "top",
    //         showClass: {
    //             icon: "animated heartBeat delay-1s",
    //         },
    //         icon: "warning",
    //         text: "Please check information again",
    //         showConfirmButton: false,
    //         timer: 1000,
    //     });
    // Swal.fire({
    //     position: 'center',
    //     allowEscapeKey: false,
    //     allowOutsideClick: false,
    //     showConfirmButton: false,
    //     didOpen: () => {
    //         Swal.showLoading();
    //     }
    // });
    // try {
    //     const formData = new FormData();
    //     formData.append("PhoneEmail", form.email);
    //     formData.append("password", form.password);

    //     const response = await Http.post<IAuth>(`login`, formData);

    //     if (response.data.success) {
    //         Cookie.set("token", response.data.token, 10);
    //         Cookie.set("session_id", Crypt.encrypt(JSON.stringify(response.data.user.id)), 10);
    //         const tokenCookie = Cookie.get("token");
    //         console.log(tokenCookie)
    //         httpAuth.defaults.headers.common["Authorization"] = `Bearer ${tokenCookie}`;
    //         store.dispatch(AUTH_STORE.ACTIONS.SET_TOKEN, response.data.token)
    //         Swal.close();
    //         Swal.fire({
    //             toast: true,
    //             position: "top",
    //             showClass: {
    //                 icon: "animated heartBeat delay-1s",
    //             },
    //             icon: "success",
    //             text: "Welcome back " + response.data.user.name,
    //             showConfirmButton: false,
    //             timer: 1000,
    //         });
    //         router.push("/");
    //     } else {
    //         Swal.fire({
    //             toast: true,
    //             position: "top",
    //             showClass: {
    //                 icon: "animated heartBeat delay-1s",
    //             },
    //             icon: "warning",
    //             text: response.data.message,
    //             showConfirmButton: false,
    //             timer: 1000,
    //         });
    //         if (response.data.validation_error) {
    //             if (response.data.data.PhoneEmail) {
    //                 message.value.email_validate = response.data.data.PhoneEmail[0];
    //             }
    //             if (response.data.data.password) {
    //                 for (let index = 0; index < response.data.data.password.length; index++) {
    //                     message.value.password_validate.push(response.data.data.password[0]);
    //                 }
    //             }
    //         }
    //     }
    // } catch (error) {
    //     console.log(error);
    //     Swal.fire({
    //         toast: true,
    //         position: "top",
    //         showClass: {
    //             icon: "animated heartBeat delay-1s",
    //         },
    //         icon: "warning",
    //         text: "Email or password is invalid. Password should be atleast 8 characters.",
    //         showConfirmButton: false,
    //         timer: 1000,
    //     });
    // }
}
</script>

<template>
    <div class="bg-field flex flex-col items-center justify-center">
        <div class="flex flex-col justify-end h-[8.5rem] w-full p-3 bg-no-repeat bg-right">
            <h1 class="text-md font-bold leading-tight tracking-tight text-gray-900 md:text-2xl uppercase">
                login
            </h1>
            <p class="text-sm font-medium text-gray-900">Enter your email and password</p>
        </div>
        <div class="w-full bg-white xl:p-0">
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <div class="space-y-4 md:space-y-6">
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                        <input v-model="form.email" ref="input1" @keyup.enter="login()" type="email" name="email" id="email"
                            class="bg-field text-gray-900 sm:text-sm block w-full p-4" placeholder="example@gmail.com"
                            required />
                        <p class="text-sm text-red-400 mt-3">{{ message.email_validate }}</p>
                    </div>
                    <div>
                        <label htmlFor="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                        <input v-model="form.password" ref="input2" @keyup.enter="login()" type="password" name="password"
                            id="password" placeholder="••••••••" class="bg-field text-gray-900 sm:text-sm block w-full p-4"
                            required />
                        <p class="text-sm text-red-400 mt-3" v-for="(item, index) in message.password_validate"
                            :key="index">{{
                                item }}</p>
                    </div>
                    <button @click="login()"
                        class="w-full text-white bg-main focus:outline-none font-medium rounded-sm text-sm px-5 py-4 text-center">
                        Sign in
                    </button>
                    <div class="px-5 py-3 w-full">
                        <div
                            class="text-center before:text-gray-500 before:content-['\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0'] before:line-through after:text-gray-500 after:content-['\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0'] after:line-through">
                            <span class="text-gray-500"> Or </span>
                        </div>
                    </div>
                    <RouterLink to="/phone_login">
                        <div class="flex items-center justify-center gap-4 border border-solid border-main p-3 rounded">
                            <img :src="Upload.icon('phone.svg')" alt="" class="w-[25px]">
                            <p class=" text-sm font-semibold text-gray-500 text-center">
                                Login with Phone Number
                            </p>
                        </div>
                    </RouterLink>
                    <p class="text-sm font-normal text-gray-500 mt-10 text-center">
                        Din't have an account yet?
                        <RouterLink to="/register" class="font-medium text-main hover:underline ml-1">Register
                        </RouterLink>
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>
