<script setup lang="ts">
import Swal from 'sweetalert2';
import { Upload } from '../../service/helpers/index';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { adminAuthService } from '../../service/api/modules/auth-admin.api'
import { ILoginMessage } from '../../types/AdminAuth'
import { API_URL } from '@/config/api.config';
// // import Profile from '../../setting/profile.js';

const router = useRouter();

// const validateUsername = ref<string>('')

let message = ref<ILoginMessage>({
    name: [],
    password: []
})
let visible = ref<boolean>(false);

interface IForm {
    username: string;
    password: string;
}

let form = ref<IForm>({
    username: '',
    password: ''
});

let loading = ref<boolean>(false);

// let profile = ref({});

onMounted(async () => {
    if (await adminAuthService.isAuthenticated()) {
        router.replace("/admin")
    }
});

const login = async () => {
    loading.value = true;
    // if (form.value.username === "" || form.value.password === "") return Swal.fire({
    //     toast: true,
    //     position: 'top',
    //     showClass: {
    //         icon: 'animated heartBeat delay-1s'
    //     },
    //     icon: 'warning',
    //     text: 'Please check information again',
    //     showConfirmButton: false,
    //     timer: 1000
    // });
    const formData = new FormData();
    formData.append('name', form.value.username);
    formData.append('password', form.value.password);

    const [error, data] = await adminAuthService.login(formData)
    if (error) console.log(error);
    else {
        if (data.success) {
            sessionStorage.setItem("adminToken", data.token);
            Swal.fire({
                toast: true,
                position: 'top',
                showClass: {
                    icon: 'animated heartBeat delay-1s'
                },
                icon: 'success',
                text: 'Welcome back' + data.user.name,
                showConfirmButton: false,
                timer: 1000
            });
            router.replace("/admin");
        } else {
            // for (let i = 0; i < data.data.name.length; i++) {
            //     validateUsername.value += data.data.name[i];
            // }
            message.value.name = data.data.name;
            message.value.password = data.data.password;
        }
    }
    loading.value = false;
}
</script>

<template>
    <section class="h-screen bg-no-repeat bg-opacity-0 bg-[]" :style="'background-size: 100% 100%'">
        <v-sheet class=" bg-slate-200 flex flex-row items-center justify-center md:px-6 md:py-8 mx-auto h-screen">
            <v-card
                class="flex flex-col md:flex-row gap-3 w-full h-full items-center bg-white rounded-lg shadow-[#E1E1E1_0px_1px_8px] md:mt-0 sm:max-w-[60rem] xl:p-0 overflow-hidden">
                <div
                    class="d-flex flex-column w-full h-full flex justify-center items-center bg-slate-300 p-6 space-y-4 md:space-y-6 sm:p-8">
                    <div class="w-[6rem] h-[6rem]">
                        <v-img class="w-full h-full" :src="'../images/merchant-app.png'" :width="100" aspect-ratio="1/1"
                            cover></v-img>
                    </div>
                    <h1 class="text-header font-semibold text-gray-700 capitalize">Welcome to adminstrator page</h1>
                </div>
                <div class="w-full h-full flex justify-center items-center">
                    <!-- <RouterLink to='/' class="flex items-center text-2xl font-semibold text-white">
                        <img class="w-8 h-8 mr-2" :src="profile.image" alt="logo" />
                        {{ profile.name }}
                    </RouterLink> -->
                    <v-sheet class="p-6 space-y-4 md:space-y-6 sm:p-8" width="377">
                        <div class="flex flex-row gap-5 items-center justify-center">
                            <h2 class="text-title text-center font-semibold tracking-tight text-gray-800 md:text-header">
                                Sign in as administator
                            </h2>
                        </div>
                        <v-container>
                            <!-- <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Username</label> -->
                            <v-text-field v-model="form.username" label="Username" variant="outlined" color="primary"
                                hide-details="auto" class="my-5"></v-text-field>
                            <!-- <input type="email" name="email" id="email"
                                        class="bg-gray-50 border border-solid border-gray-300 text-gray-900 sm:text-sm rounded-sm focus:ring-primary focus:border-primary block w-full p-2.5 "
                                        placeholder="" required /> -->
                            <p v-for="(item, index) in message.name" class="font-semibold text-sm mt-2 text-red-600 mb-2"
                                :key="index">{{ item }}</p>

                            <!-- <v-list-item v-for="(item, index) in message.name" :key="index"
                                    :subtitle="item[index]"></v-list-item> -->
                            <!-- <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label> -->
                            <v-text-field v-model="form.password" label="Password" variant="outlined"
                                :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                                :type="visible ? 'text' : 'password'" @click:append-inner="visible = !visible"
                                hide-details="auto" class="my-5"></v-text-field>
                            <!-- <input v-model="form.password" type="password" name="password" id="password"
                                        placeholder="••••••••"
                                        class="bg-gray-50 border border-solid border-gray-300 text-gray-900 sm:text-sm rounded-sm focus:ring-primary focus:border-primary block w-full p-2.5 "
                                        required /> -->
                            <p v-for="(item, index) in message.password"
                                class="font-semibold text-sm mt-2 text-red-600 mb-2" :key="index">{{ item }}
                            </p>
                            <v-btn @click="login()" color="success" block :loading="loading" size="large">
                                <!-- <div class="h-[2.5rem] leading-[2.5rem] basis-[100%] pl-[2.5rem]">Login</div>
                                    <div class="h-[2.5rem] leading-[2.5rem] flex justify-center basis-[15%]">
                                        <img :src="Upload.icon('rightarrow.svg')" alt="" class="w-[25px]">
                                    </div> -->
                                Login
                                <!-- <v-icon icon="mdi-chevron-right" end></v-icon> -->
                            </v-btn>
                        </v-container>
                    </v-sheet>
                </div>
            </v-card>
        </v-sheet>
    </section>
</template>