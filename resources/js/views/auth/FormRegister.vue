<script setup lang="ts">
import Swal from "sweetalert2";
import { onMounted, ref, reactive } from "vue";
import { useRouter, useRoute } from "vue-router";
import { useStore } from "@/use/useStore";
import { AUTH_STORE } from "@/store/constants";
import { userService } from "@/services/api/modules/user.api";

const store = useStore();
const route = useRoute();
const router = useRouter();
const input1 = ref<HTMLInputElement | null>(null);

let loading = ref<boolean>(false);

interface Form {
    full_name: string,
    dob: string,
    default_password: string;
}

let form: Required<Form> = reactive({
    full_name: "",
    dob: "",
    default_password: ""
});

onMounted(() => {
    checkPassword();
    input1.value?.focus();
});

function checkPassword() {
    if (store.getters[AUTH_STORE.GETTERS.GET_PASSWORD_PASS] === "") {
        router.replace("/phone_login");
    } else {
        form.default_password = store.getters[AUTH_STORE.GETTERS.GET_PASSWORD_PASS];
    }
}

const saveUser = async () => {
    if (form.full_name == "" || form.dob == "" || form.default_password == "")
        return Swal.fire({
            toast: true,
            position: "top",
            showClass: {
                icon: "animated heartBeat delay-1s",
            },
            icon: "warning",
            text: "Please check information again",
            showConfirmButton: false,
            timer: 1000,
        });
    loading.value = true;
    const response = await userService.registerUser(form.full_name, form.dob, form.default_password, route.params.number as string)
    if (response?.success) {
        Swal.close();
        store.dispatch(AUTH_STORE.ACTIONS.SET_TOKEN, response.token);
        loading.value = false;
        router.replace("/");
    }
}

</script>

<template>
    <div class="bg-field flex flex-col items-center justify-center">
        <div class="flex flex-col justify-end h-[8.5rem] w-full p-3 bg-no-repeat bg-right">
            <h1 class="text-md font-bold leading-tight tracking-tight text-gray-900 md:text-2xl uppercase">
                Information
            </h1>
            <p class="text-sm font-medium text-gray-900">Enter your name and date of birth</p>
        </div>
        <div class="w-full bg-white xl:p-0">
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                <div class="space-y-4 md:space-y-6">
                    <div>
                        <v-text-field variant="underlined" v-model="form.full_name" label="Name" ref="input1" type="text"
                            name="full_name" id="full_name" class=" text-gray-900" placeholder="" required></v-text-field>
                    </div>
                    <p class="block text-sm font-medium text-gray-900 capitalize">Date of
                        Birth</p>
                    <v-text-field class="spacing-playground pa-0" variant="underlined" type="date" format="dd-mm-yy"
                        v-model="form.dob" placeholder="Date of birth" required></v-text-field>
                    <div>
                    </div>
                    <v-btn @click="saveUser()" :loading="loading" color="blue" size="large" flat block>
                        Create now
                    </v-btn>
                </div>
            </div>
        </div>
    </div>
</template>
