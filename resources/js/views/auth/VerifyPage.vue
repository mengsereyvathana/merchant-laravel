<script setup lang="ts">
import { onMounted, ref } from "vue";
import { useRoute, useRouter } from "vue-router";
import 'firebase/compat/auth';
import { Http } from "@/services/api/ApiDataService";
import { IChecker } from "@/types/Checker";
import Swal from 'sweetalert2';

const route = useRoute();
const router = useRouter();

let counter = ref(60);

const otpInput = ref(["", "", "", "", "", ""]);
const inputRefs = ref<HTMLInputElement[]>([]);
const inputs = ["input1", "input2", "input3", "input4"];

const errMsg = ref<string>("");

let isResend = ref<boolean>(false)

onMounted(() => {
    inputRefs.value[0].focus();
    let interval = setInterval(() => {
        counter.value = counter.value - 1;
        if (counter.value == 0) {
            isResend.value = true;
            Swal.fire({
                toast: true,
                position: "top",
                showClass: {
                    icon: "animated heartBeat delay-1s",
                },
                icon: "info",
                text: "OTP was expired",
                showConfirmButton: false,
                timer: 1000,
            });
            clearInterval(interval);
        }
    }, 1000);
})

const verifyCode = async () => {
    // if (otpInput.value[0] == "" || otpInput.value[1] == "" || otpInput.value[2] == "" || otpInput.value[3] == "")
    //     return Swal.fire({
    //         toast: true,
    //         position: "top",
    //         showClass: {
    //             icon: "animated heartBeat delay-1s",
    //         },
    //         icon: "warning",
    //         text: "Please complete your otp",
    //         showConfirmButton: false,
    //         timer: 1000,
    //     });
    // let otp = otpInput.value[0] + otpInput.value[1] + otpInput.value[2] + otpInput.value[3];
    // const formData = new FormData();

    // formData.append("emailPhone", route.params.email as string);
    // formData.append("otp", otp);
    // try {
    //     const response = await Http.post<IChecker>(`verify_email_otp`, formData);
    //     if (response.data.success) {
    //         Swal.fire({
    //             toast: true,
    //             position: "top",
    //             showClass: {
    //                 icon: "animated heartBeat delay-1s",
    //             },
    //             icon: "success",
    //             text: response.data.message,
    //             showConfirmButton: false,
    //             timer: 1000,
    //         })
    //         router.push("/login");
    //     }
    //     else {
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
    //     }
    // } catch (error) {
    //     console.log(error)
    // }
}

const resendCode = async () => {
    // try {
    //     isResend.value = false;
    //     counter.value = 60;
    //     otpInput.value[0] = otpInput.value[1] = otpInput.value[2] = otpInput.value[3] = "";

    //     const formData = new FormData();
    //     formData.append("emailPhone", route.params.email as string);

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
    //     const response = await Http.post<IChecker>('resend_otp', formData)
    //     if (response.data.success) {
    //         let interval = setInterval(() => {
    //             counter.value = counter.value - 1;
    //             if (counter.value == 0) {
    //                 isResend.value = true;
    //                 Swal.fire({
    //                     toast: true,
    //                     position: "top",
    //                     showClass: {
    //                         icon: "animated heartBeat delay-1s",
    //                     },
    //                     icon: "info",
    //                     text: "OTP was expired",
    //                     showConfirmButton: false,
    //                     timer: 1000,
    //                 });
    //                 clearInterval(interval);
    //             }
    //         }, 1000);

    //         return Swal.fire({
    //             toast: true,
    //             position: "top",
    //             showClass: {
    //                 icon: "animated heartBeat delay-1s",
    //             },
    //             icon: "success",
    //             text: "We send otp to your email",
    //             showConfirmButton: false,
    //             timer: 1000,
    //         })
    //     } else {
    //         return Swal.fire({
    //             toast: true,
    //             position: "top",
    //             showClass: {
    //                 icon: "animated heartBeat delay-1s",
    //             },
    //             icon: "error",
    //             text: response.data.message,
    //             showConfirmButton: false,
    //             timer: 1000,
    //         });
    //     }
    // } catch (error) {
    //     console.log(error)
    // }
}

const setInputRef = (index: number) => (el: any) => {
    inputRefs.value[index] = el;
};

const onlyNumbers = (index: number) => {
    otpInput.value[index] = otpInput.value[index].replace(/[^0-9]/g, "");
};

const handleInput = (index: number) => {
    if (index < 4 && otpInput.value[index - 1]) {
        if (inputRefs.value[index]) {
            inputRefs.value[index].focus();
        }
    }
};

const handleDelete = (index: number) => {
    if (index > 0 && !otpInput.value[index]) {
        otpInput.value[index - 1] = "";
        if (inputRefs.value[index - 1] instanceof HTMLInputElement) {
            (inputRefs.value[index - 1] as HTMLInputElement).focus();
        }
    }
};
</script>

<template>
    <section class="bg-white h-screen">
        <div class="flex flex-col items-center justify-center px-2 py-8 mx-auto h-screen lg:py-0">
            <div class="w-full bg-white rounded-lg md:mt-0 sm:max-w-md xl:p-0">
                <div class="p-3 space-y-4 md:space-y-6 sm:p-8">
                    <div class="flex flex-col items-center justify-center text-center space-y-2">
                        <div class="font-semibold text-3xl">
                            <p>Phone Verification</p>
                        </div>
                        <div class="flex flex-row text-sm font-medium text-gray-400">
                            <p>We have sent a code to your SMS {{ route.params.email }}</p>
                        </div>
                    </div>
                    <div class="space-y-4 md:space-y-6">
                        <div class="flex flex-col space-y-5">
                            <div class="flex flex-row items-center justify-between mx-auto w-full">
                                <div class="w-10 h-10" v-for="(input, index) in inputs" :key="index">
                                    <input :ref="setInputRef(index)" @keyup="handleInput(index + 1)"
                                        @keyup.enter="verifyCode()" @keydown.delete="handleDelete(index)"
                                        @input="onlyNumbers(index)" v-model="otpInput[index]" :autofocus="index === 0"
                                        maxLength="1"
                                        class="w-full h-full flex flex-col items-center justify-center text-center px-3 outline-none input text-xl rounded border-solid border-2 focus:ring-main focus:border-main"
                                        type="tel" name="" id="" />
                                </div>
                            </div>

                            <div class="flex flex-col space-y-5">
                                <button @click="verifyCode()"
                                    class="flex flex-row items-center justify-center text-center w-full border rounded-lg outline-none py-3 bg-main border-none text-white text-base shadow-sm">
                                    Verify
                                </button>
                                <div
                                    class="flex flex-row items-center justify-center text-center text-sm font-medium space-x-1 text-gray-500">
                                    <p>Didn't recieve code?</p>
                                    <span v-if="isResend === false" class="flex flex-row items-center text-main"
                                        target="_blank" rel="noopener noreferrer">OTP expired in {{
                                            counter
                                        }}s</span>
                                    <span v-if="isResend" @click="resendCode()"
                                        class="flex flex-row items-center text-main cursor-pointer hover:underline">Resend</span>
                                </div>
                                <p class="text-red-600 m-auto text-lg">{{ errMsg }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<style scoped>
input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
    display: none;
}
</style>