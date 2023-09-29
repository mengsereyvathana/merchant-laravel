<script setup lang="ts">
import { useRouter } from 'vue-router';
import { onMounted, ref } from 'vue';
import { getAuth, setPersistence, browserLocalPersistence, onAuthStateChanged, RecaptchaVerifier, AuthError } from "firebase/auth";
import { Cookie, Crypt } from '@/services/helper/index';
import { ILoginPhone } from '@/types/LoginPhone';
import { useStore } from '@/use/useStore';
import { AUTH_STORE } from '@/store/constants';
import { userService } from '@/services/api/modules/user.api';
import { handleErrorMsg } from '@/services/helper/handleErrorMsg'
import Swal from 'sweetalert2';
// import { httpAuth } from '@/services/api/http.common';

// type FirebaseApp = NonNullable<Parameters<typeof getAuth>[0]>;
type Auth = ReturnType<typeof getAuth>
let auth: Auth
let appVerifier: RecaptchaVerifier | null = null;

interface Country {
    code: string;
    name: string;
}
const country: Country[] = [
    {
        code: '+855',
        name: 'Cambodia',
    },
    {
        code: '+000',
        name: 'Untitle',
    }
]
const selectedCountry = ref<Country | null>({
    code: '+855',
    name: 'Cambodia',
});

const store = useStore();
const router = useRouter();

const otpInput = ref<string[]>(["", "", "", "", "", ""]);
const inputRefs = ref<HTMLInputElement[]>([]);
const inputs = ["input1", "input2", "input3", "input4", "input5", "input6"];

const phoneNumber = ref<string>('');
const verificationId = ref<string | null>(null);
const errMsg = ref<string>("");
const token = ref<string | null>("");

let showMsg = ref<string>("");
let counter = ref<number>(60);
let nextStep = ref<boolean>(false)
let interval: NodeJS.Timeout;

const isLoggedIn = ref<boolean>(false);
const isOnVerify = ref<boolean>(false);
const onResend = ref<boolean>(false);

onMounted(async () => {
    auth = getAuth();
    await setPersistence(auth, browserLocalPersistence);
    onAuthStateChanged(auth, (user) => {
        if (user) {
            checkAuth();
            isLoggedIn.value = true;
        }
    });
});

const checkAuth = async () => {
    token.value = store.getters[AUTH_STORE.GETTERS.GET_TOKEN]
    const [error, data] = await userService.checkAuth();
    if (error) console.log(error);
    else {
        if (data.success) {
            Swal.fire({
                toast: true,
                position: "top",
                showClass: {
                    icon: "animated heartBeat delay-1s",
                },
                icon: "info",
                text: "You already have an account.",
                showConfirmButton: false,
                timer: 1000,
            });
            router.replace("/")
        } else {
            router.replace("/phone_login")
        }
    }
}

const sendCode = async () => {
    try {
        if (selectedCountry.value === null || phoneNumber.value === "" || phoneNumber.value.length > 9) {
            return Swal.fire({
                toast: true,
                position: "top",
                showClass: {
                    icon: "animated heartBeat delay-1s",
                },
                icon: "warning",
                text: "Please enter a phone number",
                showConfirmButton: false,
                timer: 1000,
            });
        }
        Swal.fire({
            position: 'center',
            allowEscapeKey: false,
            allowOutsideClick: false,
            showConfirmButton: false,
            timer: 4000,
            didOpen: () => {
                Swal.showLoading();
            }
        });
        showMsg.value = "Please complete below:";
        isOnVerify.value = true;
        // appVerifier?.render();
        if (!appVerifier) {
            appVerifier = await userService.createRecaptchaVerifier(appVerifier, auth)
            if (await appVerifier.verify()) {
                Swal.fire({
                    position: 'center',
                    allowEscapeKey: false,
                    allowOutsideClick: false,
                    showConfirmButton: false,
                    timer: 4000,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
                nextStep.value = true;
                onResend.value = true;
                counter.value = 60;

                interval = setInterval(() => {
                    counter.value = counter.value - 1;
                    if (counter.value == 0) {
                        onResend.value = false;
                        Swal.fire({
                            toast: true,
                            position: "top",
                            showClass: {
                                icon: "animated heartBeat delay-1s",
                            },
                            icon: "info",
                            text: "OTP has expired",
                            showConfirmButton: false,
                            timer: 1000,
                        });
                        clearInterval(interval);
                    }
                }, 1000);
            }
        }
        const res = await userService.sendCode(selectedCountry.value?.code + phoneNumber.value, appVerifier, auth);
        if (res?.verificationId) {
            verificationId.value = res.verificationId;
            Cookie.set("onVerification", "true", 60);
            Swal.close();
            Swal.fire({
                toast: true,
                position: 'top',
                icon: 'success',
                showConfirmButton: false,
                text: "We have sent a code to your SMS",
                timer: 1000,
            })
            console.log('Code sent successfully.');
        }
    } catch (error) {
        console.error('Error sending code:', error);
    }
};

const verifyCode = async () => {
    if (otpInput.value[0] == "" || otpInput.value[1] == "" || otpInput.value[2] == "" || otpInput.value[3] == "" || otpInput.value[4] == "" || otpInput.value[5] == "")
        return Swal.fire({
            toast: true,
            position: "top",
            showClass: {
                icon: "animated heartBeat delay-1s",
            },
            icon: "warning",
            text: "Please complete your OTP",
            showConfirmButton: false,
            timer: 1000,
        });
    Swal.fire({
        position: 'center',
        allowEscapeKey: false,
        allowOutsideClick: false,
        showConfirmButton: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    let code = otpInput.value.join("");
    if (verificationId.value) {
        try {
            const response = await userService.verifyCode(verificationId.value, code, auth)
            if (response.user) {
                Swal.close();
                //Create user in database
                try {
                    const formData = new FormData();
                    formData.append("phoneNumber", `+855${phoneNumber.value}`);
                    formData.append("pss", "12345678");

                    Swal.fire({
                        position: 'center',
                        allowEscapeKey: false,
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => {
                            window.Swal.showLoading();
                        }
                    });
                    const [error, data] = await userService.loginWithPhone(formData)
                    if (error) console.log(error);
                    else {
                        if (data.success) {
                            if (data.is_new) {
                                Swal.close();
                                store.dispatch(AUTH_STORE.ACTIONS.SET_PASSWORD_PASS, "12345678")
                                router.push('/form_register/' + phoneNumber.value)
                            } else {
                                Swal.close();
                                // httpAuth.defaults.headers.common["Authorization"] = `Bearer ${data.token}`
                                Cookie.set("token", data.token, 10);
                                Cookie.set("session_id", Crypt.encrypt(JSON.stringify(data.data.id)), 10);
                                store.dispatch(AUTH_STORE.ACTIONS.SET_TOKEN, data.token)
                                clearInterval(interval)
                                router.push('/');
                            }
                        }
                    }
                } catch (error) {
                    console.log(error)
                }
            }
        } catch (error) {
            Swal.close();
            const errorResponse = handleErrorMsg((error as AuthError).code);
            Swal.fire({
                toast: true,
                position: "top",
                showClass: {
                    icon: "animated heartBeat delay-1s",
                },
                icon: "warning",
                text: errorResponse?.errorMsg,
                showConfirmButton: false,
                timer: 3000,
            })
        }
    }
}

function countryProps(item: any) {
    return {
        subtitle: item.name,
        title: item.code,
    }
}

const setInputRef = (index: number) => (el: any) => {
    inputRefs.value[index] = el;
};

const onlyNumbers = (index: number) => {
    otpInput.value[index] = otpInput.value[index].replace(/[^0-9]/g, "");
};

const handleInput = (index: number) => {
    if (index < 6 && otpInput.value[index - 1]) {
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

const resendCode = () => {
    otpInput.value[0] = otpInput.value[1] = otpInput.value[2] = otpInput.value[3] = otpInput.value[4] = otpInput.value[5] = "";
    sendCode();
    onResend.value = false;
};
</script>

<template>
    <section v-if="nextStep === false">
        <div class="bg-field flex flex-col items-center justify-center">
            <div class="flex flex-col justify-end h-[8.5rem] w-full p-3 bg-no-repeat bg-right">
                <h1 class="text-md font-bold leading-tight tracking-tight text-gray-900 md:text-2xl uppercase">
                    login
                </h1>
                <p class="text-sm font-medium text-gray-900">Enter your phone number</p>
            </div>
            <div class="w-full bg-white xl:p-0">
                <div class="p-6 space-y-4 md:space-y-6 sm:p-8">
                    <div class="space-y-4 md:space-y-6">
                        <!-- <label for="phone" class="block mb-2 text-sm font-medium text-gray-900">Phone</label> -->
                        <div class="flex justify-between items-center">
                            <!-- <p class="font-semibold bg-main p-4 text-white rounded-sm">+855</p> -->
                            <!-- <input label="First name" v-model="phoneNumber" ref="input1" type="tel" name="phone" id="phone"
                                class="" placeholder="eg, 99 999 9999" required /> -->
                            <v-select class="fit" variant="underlined" label="Country" v-model="selectedCountry"
                                :items="country" :item-props="countryProps" hide-details="auto">
                            </v-select>
                            <v-text-field variant="outlined" class="pa-2" v-model="phoneNumber" ref="input1"
                                hide-details="auto" type="tel" placeholder="XX XXX XXXX"
                                label="Phone Number"></v-text-field>

                        </div>
                        <button @click="sendCode()" v-ripple-init
                            class="ripple-effect w-full text-white bg-main focus:outline-none font-medium rounded-sm text-sm px-5 py-4 text-center ">
                            Sign in
                        </button>
                        <div class="text-main">{{ showMsg }}</div>
                        <!-- <div v-if="isOnVerify === false">
                            <div class="px-5 py-3 w-full">
                                <div
                                    class="text-center before:text-gray-500 before:content-['\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0'] before:line-through after:text-gray-500 after:content-['\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0\00a0'] after:line-through">
                                    <span class="text-gray-500"> Or </span>
                                </div>
                            </div>
                            <RouterLink to="/login">
                                <div
                                    class="flex items-center justify-center gap-4 border border-solid border-main p-3 rounded">
                                    <img :src="Upload.icon('email.svg')" alt="" class="w-[25px]">
                                    <p class=" text-sm font-semibold text-gray-500 text-center">
                                        Login with Email/Password
                                    </p>
                                </div>
                            </RouterLink>
                            <p class="text-sm font-normal text-gray-500 mt-5 text-center">
                                Don't have an account yet?
                                <RouterLink to="/register" class="font-medium text-main hover:underline ml-1">Register
                                </RouterLink>
                            </p>
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="bg-white" v-else>
        <div class="flex flex-col items-center justify-center px-2 py-8 mx-auto h-screen lg:py-0">
            <div class="w-full bg-white rounded-lg md:mt-0 sm:max-w-md xl:p-0">
                <div class="p-3 space-y-4 md:space-y-6 sm:p-8">
                    <div class="flex flex-col items-center justify-center text-center space-y-2">
                        <div class="font-semibold text-3xl">
                            <p>Phone Verification</p>
                        </div>
                        <div class="flex flex-row text-sm font-medium text-gray-400">
                            <p>We have sent a code to your SMS +855{{ phoneNumber }}</p>
                        </div>
                        <p class="text-red-600 m-auto text-lg font-semibold">{{ errMsg }}</p>
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
                                <button @click="verifyCode()" v-ripple-init
                                    class="flex flex-row items-center justify-center text-center w-full border rounded-lg outline-none py-3 bg-main border-none text-white text-base shadow-sm ripple-effect">
                                    Verify
                                </button>
                                <div
                                    class="flex flex-row items-center justify-center text-center text-sm font-medium space-x-1 text-gray-500">
                                    <p>Didn't recieve code?</p>
                                    <span v-if="counter > 0" class="flex flex-row items-center text-main" target="_blank"
                                        rel="noopener noreferrer">OTP expired in {{
                                            counter
                                        }}s</span>
                                    <span v-else @click="resendCode()"
                                        class="flex flex-row items-center text-main cursor-pointer hover:underline">Resend</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- recaptcha -->
    <section>
        <div id="recaptcha-container"></div>
    </section>

    <!-- <div>
        <div v-if="nextStep === false" id="recaptcha-container"></div>
        <input v-model="phoneNumber" type="tel" placeholder="Phone Number">
        <button @click="sendCode">Send Verification Code</button>
        <input v-model="verificationCode" type="number" placeholder="Verification Code">
        <button @click="verifyCode">Verify Code</button>
        <div class="bg-red" v-if="errMsg">{{ errMsg }}</div>
        <div v-if="nextStep === false">
        </div>
        <div v-else>
        </div>
    </div> -->
</template>



