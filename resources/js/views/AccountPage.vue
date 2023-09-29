<script setup lang="ts">
import Swal from 'sweetalert2';
import { ref, onMounted } from 'vue'
import { useRouter } from 'vue-router';
import { IUser } from '../types/UserDetail'
import { getAuth, onAuthStateChanged, setPersistence, browserLocalPersistence } from 'firebase/auth';
import { userService } from '@/services/api/modules/user.api';
import { useStore } from '@/use/useStore';
import { AUTH_STORE } from '@/store/constants';
import { Upload } from '@/services/helper';

type Auth = ReturnType<typeof getAuth>
let auth: Auth;

const router = useRouter()
const store = useStore();
const isLoggedIn = ref<boolean>(false)
const user = ref<Partial<IUser>>({});

onMounted(async () => {
    auth = getAuth();
    await setPersistence(auth, browserLocalPersistence);
    onAuthStateChanged(auth, (user) => {
        if (user) {
            getUser();
            isLoggedIn.value = true;
        }
    });
});

const getUser = async () => {
    const [error, data] = await userService.getUser();
    if (error) console.log(error);
    else {
        if (data.success) {
            user.value = data.data;
        }
    }
};

const handleSignOut = async () => {
    const result = await Swal.fire({
        title: "Logout",
        text: "Are you sure, you want to logout?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Logout",
    });
    if (result.isConfirmed) {
        const response = await userService.logoutFirebase(auth)
        if (response.success) {
            store.dispatch(AUTH_STORE.ACTIONS.SET_TOKEN, null);
            store.dispatch(AUTH_STORE.ACTIONS.SET_USER_ID, null);
            Swal.fire({
                toast: true,
                position: 'top',
                icon: 'success',
                text: 'Successfully logout',
                showConfirmButton: false,
                timer: 1000,
            })
        }
        router.replace("/phone_login");
    }
}

</script>

<template>
    <div v-if="isLoggedIn" class="flex flex-col gap-3 bg-white">
        <div class="flex flex-col pt-4 px-4 gap-3">
            <div class="bg-white shadow-lg first:border-t-0 p-4 pb-0 rounded-lg">
                <div class="uppercase pb-4 font-semibold flex justify-center">
                    <button class='cursor-default w-16 h-16'>
                        <img :src="user.image"
                            class='w-16 h-16 bg-[#f3f5f9] rounded-full text-center leading-10 md:leading-[46px] cursor-pointer object-cover' />
                    </button>
                </div>
                <div class="border-t border-solid flex gap-4 items-center py-4">
                    <img :src="Upload.icon('user.svg')" alt="" class="w-[23px] h-[23px]">
                    <p class="text-md font-medium">{{ user.name }}</p>
                </div>
                <div class="border-t border-solid flex gap-4 items-center py-4">
                    <img :src="Upload.icon('calender.svg')" alt="" class="w-[23px] h-[23px]">
                    <p class="text-md font-medium">{{ user.dob }}</p>
                </div>
                <div class="border-t border-solid flex gap-4 items-center py-4">
                    <img :src="Upload.icon('phonenumber.svg')" alt="" class="w-[23px] h-[23px]">
                    <p class="text-md font-medium">{{ user.email ?? user.phone }}</p>
                </div>
            </div>
        </div>
        <div class="flex flex-col gap-2 px-4">
            <div class="w-full">
                <RouterLink to="/my_order">
                    <button
                        class="w-full text-white bg-main focus:outline-none font-medium rounded-lg text-sm px-5 py-4 text-center">My
                        Order</button>
                </RouterLink>
            </div>
            <div class="w-full">
                <button @click="handleSignOut()"
                    class="w-full text-white bg-red-600 focus:outline-none font-medium rounded-lg text-sm px-5 py-4 text-center">
                    Logout</button>
            </div>
        </div>
    </div>
</template>