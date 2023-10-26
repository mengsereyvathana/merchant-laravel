<script setup lang="ts">
import Swal from 'sweetalert2';
import { useRouter } from 'vue-router';
import { onMounted, ref } from 'vue';
import { getAuth, setPersistence, browserLocalPersistence, onAuthStateChanged, RecaptchaVerifier, AuthError } from "firebase/auth";
import { Cookie, Crypt } from '@/services/helper/index';
import { useStore } from '@/use/useStore';
import { AUTH_STORE } from '@/store/constants';
import { userService } from '@/services/api/modules/user.api';
import { handleErrorMsg } from '@/services/helper/handle-error'

// type FirebaseApp = NonNullable<Parameters<typeof getAuth>[0]>;
type Auth = ReturnType<typeof getAuth>
let auth: Auth
let appVerifier: RecaptchaVerifier | null = null;

const store = useStore();
const router = useRouter();

const otpInput = ref<string[]>(["", "", "", "", "", ""]);
const inputRefs = ref<HTMLInputElement[]>([]);
const inputs = ["input1", "input2", "input3", "input4", "input5", "input6"];
const phoneNumber = ref<string>('');
const verificationId = ref<string | null>(null);
const errMsg = ref<string>("");
const onResend = ref<boolean>(false);

let loading = ref<boolean>(false);
let showMsg = ref<string>("");
let counter = ref<number>(60);
let nextStep = ref<boolean>(false);
let interval: NodeJS.Timeout;

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

onMounted(async () => {
    auth = getAuth();
    await setPersistence(auth, browserLocalPersistence);
    onAuthStateChanged(auth, async (user) => {
        if (user) {
            if (Cookie.get("token")) {
                const [error, data] = await userService.checkAuth();
                if (error) console.log(error)
                else {
                    if (data.success) {
                        router.replace("/")
                    } else {
                        router.replace("/phone_login")
                    }
                }
            }
        }
    });
});

const sendCode = async () => {
    try {
        if (selectedCountry.value === null || phoneNumber.value === "" || phoneNumber.value.length > 10) {
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

        if (phoneNumber.value.charAt(0) === "0") {
            phoneNumber.value = phoneNumber.value.substring(1);
        }

        if (!appVerifier) {
            if (phoneNumber.value.charAt(0) === "0") {
                phoneNumber.value = phoneNumber.value.substring(1);
            }
            appVerifier = await userService.createRecaptchaVerifier(appVerifier, auth)
            appVerifier.render();
            loading.value = true;
        }

        const res = await userService.sendCode(selectedCountry.value?.code + phoneNumber.value, appVerifier, auth);

        if (res?.verificationId) {
            verificationId.value = res.verificationId;

            setTimeout(() => (loading.value = false), 2000)
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

            // loading.value = false;

            // Swal.fire({
            //     toast: true,
            //     position: 'top',
            //     icon: 'success',
            //     showConfirmButton: false,
            //     text: "We have sent a code to your SMS",
            //     timer: 1000,
            // })
            // console.log('Code sent successfully.');
        }
    } catch (error) {
        console.error('Error sending code:', error);
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
    loading.value = true;
    let code = otpInput.value.join("");
    if (verificationId.value) {
        try {
            const response = await userService.verifyCode(verificationId.value, code, auth)
            if (response.user) {
                Swal.close();
                //Create user in database
                try {
                    const formData = new FormData();
                    formData.append("phoneNumber", selectedCountry.value?.code + phoneNumber.value);
                    formData.append("pss", "12345678");

                    // Swal.fire({
                    //     position: 'center',
                    //     allowEscapeKey: false,
                    //     allowOutsideClick: false,
                    //     showConfirmButton: false,
                    //     didOpen: () => {
                    //         window.Swal.showLoading();
                    //     }
                    // });
                    const [error, data] = await userService.loginWithPhone(formData)
                    if (error) console.log(error);
                    else {
                        if (data.success) {
                            if (data.is_new) {
                                Swal.close();
                                store.dispatch(AUTH_STORE.ACTIONS.SET_PASSWORD_PASS, "12345678")
                                loading.value = false;
                                router.push('/form_register/' + phoneNumber.value)
                            } else {
                                Swal.close();
                                // httpAuth.defaults.headers.common["Authorization"] = `Bearer ${data.token}`
                                Cookie.set("token", data.token, 10);
                                Cookie.set("session_id", Crypt.encrypt(JSON.stringify(data.data.id)), 10);
                                store.dispatch(AUTH_STORE.ACTIONS.SET_TOKEN, data.token)
                                clearInterval(interval)
                                loading.value = false;
                                router.push('/');
                            }
                        }
                    }
                } catch (error) {
                    console.log(error);
                }
            }
        } catch (error) {
            Swal.close();
            loading.value = false;
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
                        <div class="flex justify-between items-center">
                            <v-select class="fit" variant="outlined" label="Country" v-model="selectedCountry"
                                :items="country" :item-props="countryProps" hide-details="auto">
                            </v-select>
                            <v-text-field variant="outlined" class="pa-2" v-model="phoneNumber" ref="input1"
                                hide-details="auto" type="tel" placeholder="XX XXX XXXX"
                                label="Phone Number"></v-text-field>
                        </div>
                        <v-btn @click="sendCode()" color="blue" block single-line size="large" :loading="loading">
                            Sign in
                        </v-btn>
                        <div class="text-main">{{ showMsg }}</div>
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
                            <p>We have sent a code to your SMS {{ selectedCountry?.code + phoneNumber }}</p>
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
                                        :class="otpInput.every(element => element !== '') ? 'border-[#2196F3]' : ''"
                                        class="w-full h-full flex flex-col items-center justify-center text-center px-3 outline-none input text-xl rounded border-2 border-solid focus:ring-[#2196F3] focus:border-[#2196F3]"
                                        type="tel" name="" id="" />
                                </div>
                            </div>

                            <div class="flex flex-col space-y-5">
                                <v-btn @click="verifyCode()" :loading="loading" color="blue" size="large"
                                    :class="otpInput.every(element => element !== '') ? '' : 'opacity-70'"
                                    :disabled="!otpInput.every(element => element !== '')"
                                    class="flex flex-row items-center justify-center text-center w-full border rounded-lg outline-none py-3 bg-[#2196F3] border-none text-white text-base shadow-sm ">
                                    Verify
                                </v-btn>
                                <div
                                    class="flex flex-row items-center justify-center text-center text-sm font-medium space-x-1 text-gray-500">
                                    <p>Didn't recieve code?</p>
                                    <span v-if="counter > 0" class="flex flex-row items-center text-main" target="_blank"
                                        rel="noopener noreferrer">OTP expired in {{ counter }}s</span>
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
</template>



