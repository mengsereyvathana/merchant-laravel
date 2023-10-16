<script setup lang="ts">
import Swal from 'sweetalert2';
import { Upload } from '../../service/helpers/index';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { adminAuthService } from '../../service/api/modules/auth-admin.api'
import { ILoginMessage } from '../../types/AdminAuth'
// // import Profile from '../../setting/profile.js';

const router = useRouter();

const message = ref<ILoginMessage>({
    name: [],
    password: [],
})

interface IForm {
    username: string;
    password: string;
}

let form = ref<IForm>({
    username: '',
    password: ''
});
// let profile = ref({});

onMounted(async () => {
    if (await adminAuthService.isAuthenticated()) {
        router.replace("/admin")
    }
});

const login = async () => {
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
            message.value.name = data.data.name;
            message.value.password = data.data.password;
            Swal.fire({
                toast: true,
                position: 'top',
                showClass: {
                    icon: 'animated heartBeat delay-1s'
                },
                icon: 'warning',
                text: data.message,
                showConfirmButton: false,
                timer: 1000
            });
        }
    }
}
</script>

<template>
    <section class="bg-gray-50 h-screen bg-no-repeat bg-opacity-0 bg-[]" :style="'background-size: 100% 100%'">
        <div class=" bg-black bg-opacity-20 flex flex-row items-center justify-center px-6 py-8 mx-auto h-screen">
            <div
                class="flex flex-row gap-3 w-full h-full items-center bg-white rounded-lg shadow md:mt-0 sm:max-w-[60rem] xl:p-0 overflow-hidden">
                <div class="w-full h-full flex justify-center items-center bg-slate-300 p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1 class="text-header font-semibold text-gray-700 capitalize">Welcome to adminstrator page</h1>
                </div>
                <div class="w-full h-full flex justify-center items-center">
                    <RouterLink to='/' class="flex items-center text-2xl font-semibold text-white">
                        <!-- <img class="w-8 h-8 mr-2" :src="profile.image" alt="logo" /> -->
                        <!-- {{ profile.name }} -->
                    </RouterLink>
                    <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                        <div class="flex flex-row gap-5 items-center justify-center">
                            <div class="w-[4rem] h-[4rem]">
                                <img src="" alt="" class="w-full h-full">
                            </div>
                            <h1 class="text-title font-semibold leading-tight tracking-tight text-gray-800 md:text-header">
                                Sign in as administator
                            </h1>
                        </div>
                        <div class="space-y-4 md:space-y-6">
                            <div>
                                <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Username</label>
                                <input v-model="form.username" type="email" name="email" id="email"
                                    class="bg-gray-50 border border-solid border-gray-300 text-gray-900 sm:text-sm rounded-sm focus:ring-primary focus:border-primary block w-full p-2.5 "
                                    placeholder="" required />
                                <p v-for="(item, index) in message.name" class="font-semibold text-sm mt-2 text-red-600"
                                    :key="index">{{ item
                                    }}
                                </p>
                            </div>
                            <div>
                                <label for="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                                <input v-model="form.password" type="password" name="password" id="password"
                                    placeholder="••••••••"
                                    class="bg-gray-50 border border-solid border-gray-300 text-gray-900 sm:text-sm rounded-sm focus:ring-primary focus:border-primary block w-full p-2.5 "
                                    required />
                                <p v-for="(item, index) in message.password" class="font-semibold text-sm mt-2 text-red-600"
                                    :key="index">{{ item }}
                                </p>
                            </div>
                            <button @click="login()"
                                class="w-full text-white bg-primary focus:outline-none font-medium rounded-sm text-sm  text-center flex items-center justify-center">
                                <div class="h-[2.5rem] leading-[2.5rem] basis-[100%] pl-[2.5rem]">Login</div>
                                <div class="h-[2.5rem] leading-[2.5rem] flex justify-center basis-[15%]">
                                    <img :src="Upload.icon('rightarrow.svg')" alt="" class="w-[25px]">
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>