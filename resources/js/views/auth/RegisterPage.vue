<script setup lang="ts">
import { reactive, ref } from "vue";
import { useRouter } from "vue-router";
import { IRegister } from "@/types/Register";
import Swal from "sweetalert2";

const router = useRouter();
let firstStep = ref(true);

interface Form {
    name: string;
    email: string;
    password: string;
    confirm: string;
    image: File | null,
    otp: string,
}

let form = reactive<Required<Form>>({
    name: "",
    email: "",
    password: "",
    confirm: "",
    image: null,
    otp: "",
});

const saveUser = async () => {
    // try {
    //     if (!form.image)
    //         return Swal.fire({
    //             toast: true,
    //             position: "top",
    //             showClass: {
    //                 icon: "animated heartBeat delay-1s",
    //             },
    //             icon: "warning",
    //             text: "Please choose a profile",
    //             showConfirmButton: false,
    //             timer: 1000,
    //         });
    //     const formData = new FormData();
    //     formData.append("name", form.name);
    //     formData.append("PhoneEmail", form.email);
    //     formData.append("password", form.password);
    //     formData.append("password_confirm", form.confirm);
    //     if (form.image) {
    //         formData.append("image", form.image as File);
    //     }
    //     Swal.fire({
    //         toast: true,
    //         text: "please wait a minute!",
    //         position: "top",
    //         allowEscapeKey: false,
    //         allowOutsideClick: false,
    //         showConfirmButton: false,
    //         didOpen: () => {
    //             Swal.showLoading();
    //         },
    //     });

    //     const response = await Http.post<IRegister>(`register`, formData)
    //     if (response.data.success) {
    //         Swal.close();
    //         router.push("/verify/" + form.email);
    //     }
    //     else {
    //         return Swal.fire({
    //             toast: true,
    //             position: "top",
    //             showClass: {
    //                 icon: "animated heartBeat delay-1s",
    //             },
    //             icon: "error",
    //             text: response.data.data.PhoneEmail[0],
    //             showConfirmButton: false,
    //             timer: 1000,
    //         });
    //     }
    // } catch (error) {
    //     console.log(error)
    // }
};

function previewImage() {
    if (form.image) {
        return URL.createObjectURL(form.image);
    } else {
        //default image
        return "";
    }
}

function browseImage(e: Event) {
    const target = e.target as HTMLInputElement;
    const files: FileList | null = target.files;
    if (files && files.length > 0) {
        const file = files[0];
        const allowExtenstions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
        if (allowExtenstions.exec(target.value)) form.image = file;
    }
}

async function nextStep() {
    if (form.name == "" || form.email == "" || form.password == "" || form.confirm == "") {
        return Swal.fire({
            toast: true,
            position: "top",
            showClass: {
                icon: "animated heartBeat delay-1s",
            },
            icon: "error",
            text: "Please check information again",
            showConfirmButton: false,
            timer: 1000,
        });
    }

    if (!/^[\w.-]+@[\w-]+(\.[\w-]+)*(\.[a-zA-Z]{2,})$/.test(form.email))
        return Swal.fire({
            toast: true,
            position: "top",
            showClass: {
                icon: "animated heartBeat delay-1s",
            },
            icon: "error",
            text: "Please enter a valid email",
            showConfirmButton: false,
            timer: 1000,
        });


    if (form.password != form.confirm) {
        return Swal.fire({
            toast: true,
            position: "top",
            showClass: {
                icon: "animated heartBeat delay-1s",
            },
            icon: "error",
            text: "Password not match",
            showConfirmButton: false,
            timer: 1000,
        });
    }
    firstStep.value = false;
}
</script>

<template>
    <section v-if="firstStep" class="bg-field flex flex-col items-center justify-center">
        <div class="flex flex-col justify-end h-[8.5rem] w-full p-3 bg-no-repeat bg-right">
            <h1 class="text-md font-bold leading-tight tracking-tight text-gray-900 md:text-2xl uppercase">
                Register
            </h1>
            <p class="text-sm font-medium text-gray-900">Create your account</p>
        </div>
        <!-- <a class="flex items-center mb-6 text-2xl font-semibold text-white">
            <img class="w-14 h-8 mr-2" :src="profile.image" alt="logo" />
            {{ profile.name }}
        </a> -->
        <div class="w-full bg-white xl:p-0">
            <div class="p-6 space-y-4 md:space-y-6 sm:p-8">

                <div class="space-y-4 md:space-y-6">
                    <div>
                        <label htmlFor="name" class="block mb-2 text-sm font-medium text-gray-900">Username</label>
                        <input v-model="form.name" ref="" type="text" name="name" id="name" placeholder="e.g ..."
                            class="bg-field text-gray-900 sm:text-sm block w-full p-4" required />
                    </div>
                    <div>
                        <label for="email" class="block mb-2 text-sm font-medium text-gray-900">Email</label>
                        <input v-model="form.email" type="email" name="email" id="email"
                            class="bg-field text-gray-900 sm:text-sm block w-full p-4" placeholder="example@gmail.com"
                            required />
                    </div>
                    <div>
                        <label htmlFor="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                        <input v-model="form.password" type="password" name="password" id="password" placeholder="••••••••"
                            class="bg-field text-gray-900 sm:text-sm block w-full p-4" required />
                    </div>
                    <div>
                        <label htmlFor="password" class="block mb-2 text-sm font-medium text-gray-900">Password</label>
                        <input v-model="form.confirm" type="password" name="confirm" id="confirm" placeholder="••••••••"
                            class="bg-field text-gray-900 sm:text-sm block w-full p-4" required />
                    </div>
                    <!-- <div class="flex justify-between items-center">
                        <div class="flex gap-2 items-center">
                            <input v-model="form.remember" type="checkbox" id="remember"
                                class="accent-current border border-solid border-gray-300 rounded-sm cursor-pointer focus:ring-main" />
                            <label for="remember" class="cursor-pointer text-sm">
                                Remember me</label>
                        </div>
                        <RouterLink to="/resetpassword"
                            class="text-sm font-medium text-main cursor-pointer hover:underline">Forgot password?
                        </RouterLink>
                    </div> -->
                    <button @click="nextStep()"
                        class="w-full text-white bg-main focus:outline-none font-medium rounded-sm text-sm px-5 py-4 text-center">
                        Next
                    </button>


                    <div class="px-5 py-3 w-full">
                        <div
                            class="text-center before:text-gray-500 before:content-['\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0'] before:line-through after:text-gray-500 after:content-['\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0'] after:line-through">
                            <span class="text-gray-500"> Or </span>
                        </div>
                    </div>

                    <!-- login with google -->
                    <!-- <div class="w-full d-flex justify-center items-center">
                        <div class="flex items-center justify-center">
                            <button @click="loginWithGoogle()"
                                class="flex items-center bg-white border border-solid border-gray-200 rounded-sm px-6 py-2 text-sm font-medium text-gray-800 hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 gap-2">
                                <img :src="setIcon('google.svg')" alt="" class="w[20px] h-[20px]">
                                <span class="font-semibold">Login with Google</span>
                            </button>
                        </div>
                    </div> -->

                    <p class="text-sm font-normal text-gray-500 mt-10 text-center">
                        Already have an account?
                        <RouterLink to="/login" class="font-medium text-main hover:underline ml-1">Sign in here
                        </RouterLink>
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section v-else class="bg-white h-screen bg-no-repeat bg-opacity-0" :style="'background-size: 100% 100%'">
        <div class=" flex flex-col items-center justify-center px-6 py-8 mx-auto h-screen lg:py-0">
            <div class="flex items-center mb-6 text-2xl font-semibold text-white">
                <!-- <img class="w-8 h-8 mr-2" :src="profile.image" alt="logo" />
                {{ profile.name }} -->
            </div>
            <div class="w-full bg-white rounded-lg shadow-[0_8px_30px_rgb(0,0,0,0.12)] md:mt-0 sm:max-w-md xl:p-0">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <h1 class="text-xl font-bold leading-tight tracking-tight text-gray-900 md:text-2xl">
                        Create an account
                    </h1>
                    <div class="space-y-4 md:space-y-6">
                        <div class="w-full flex justify-center py-5">
                            <div
                                class="relative w-[150px] h-[150px] rounded-full border border-solid border-gray-300 bg-white shadow-sm">
                                <img :src="previewImage()" alt="" class="w-full h-full rounded-full object-cover p-1" />
                                <i
                                    class="absolute fa-solid fa-pen-to-square w-[40px] h-[40px] leading-[40px] text-center text-main cursor-pointer bg-white rounded-full right-0 bottom-[5px] shadow-sm border border-solid border-gray-300"></i>
                                <input @change="browseImage" type="file"
                                    class="opacity-0 absolute fa-solid fa-pen-to-square w-[40px] h-[40px] leading-[40px] text-center text-main cursor-pointer bg-white rounded-full right-0 bottom-[5px] shadow-md border border-solid border-gray-300" />
                            </div>
                        </div>
                        <button @click="saveUser()"
                            class="w-full text-white bg-main focus:outline-none font-medium rounded-lg text-sm px-5 py-2.5 text-center">
                            Create an account
                        </button>
                        <p class="text-sm font-normal text-gray-500">
                            Already have an account?
                            <RouterLink to="/login" class="font-medium text-main hover:underline ml-1">Sign in here
                            </RouterLink>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<style>
.swal2-loader {
    border-color: #42b883 transparent #42b883 transparent;
}
</style>