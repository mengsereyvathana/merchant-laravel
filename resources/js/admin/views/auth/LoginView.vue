<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { adminAuthService } from '../../service/api/modules/auth-admin.api'
import { ILoginMessage } from '../../types/AdminAuth'
import { IFormLogin } from '../../types/Form';

const router = useRouter();

let visible = ref<boolean>(false);
let loading = ref<boolean>(false);
let message = ref<ILoginMessage>({ name: [], password: [] });
let form = ref<IFormLogin>({ username: '', password: '' });

onMounted(async () => {
    if (await adminAuthService.isAuthenticated()) {
        router.replace("/admin")
    }
});

const login = async () => {
    loading.value = true;
    const formData = new FormData();
    formData.append('email', form.value.username);
    formData.append('password', form.value.password);

    const [error, data] = await adminAuthService.login(formData)
    if (error) console.log(error);
    else {
        if (data.success) {
            sessionStorage.setItem("adminToken", data.token);
            await router.replace("/admin");
        } else {
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
                    <v-sheet class="p-6 space-y-4 md:space-y-6 sm:p-8" width="377">
                        <div class="flex flex-row gap-5 items-center justify-center">
                            <h2 class="text-title text-center font-semibold tracking-tight text-gray-800 md:text-header">
                                Sign in as administator
                            </h2>
                        </div>
                        <v-container>
                            <v-text-field v-model="form.username" label="Username" variant="outlined" color="primary"
                                hide-details="auto" class="my-5"></v-text-field>
                            <p v-for="(item, index) in message.name" class="font-semibold text-sm mt-2 text-red-600 mb-2"
                                :key="index">{{ item }}</p>
                            <v-text-field v-model="form.password" label="Password" variant="outlined" color="primary"
                                :append-inner-icon="visible ? 'mdi-eye-off' : 'mdi-eye'"
                                :type="visible ? 'text' : 'password'" @click:append-inner="visible = !visible"
                                hide-details="auto" class="my-5"></v-text-field>
                            <p v-for="(item, index) in message.password"
                                class="font-semibold text-sm mt-2 text-red-600 mb-2" :key="index">{{ item }}
                            </p>
                            <v-btn @click="login()" color="success" block :loading="loading" size="large">
                                Login
                            </v-btn>
                        </v-container>
                    </v-sheet>
                </div>
            </v-card>
        </v-sheet>
    </section>
</template>
