<script setup lang="ts">
import { ref, onMounted } from 'vue';
import Sidebar from './SidebarLayout.vue';
import { Upload } from '../../service/helpers/index'
import { adminAuthService } from '../../service/api/modules/auth-admin.api';
import Swal from 'sweetalert2';
import router from '@/router';
import { IUserDataItem } from '../../types/IUserData';
import { toggleMenu, removeRail } from '@/admin/store/toggle';
import { remove } from 'lodash';
// import Profile from '../../setting/profile';
let profile = ref<object>({});
let user = ref<IUserDataItem>();
let num = ref<number>(0)

onMounted(async () => {
    // profile.value = await Profile;
    await getUser();
});

const getUser = async () => {
    const [error, data] = await adminAuthService.getUser();
    if (error) console.log(error)
    else {
        if (data.success) {
            user.value = data.data;
        }
    }
}

// function handle(value: boolean) {
//     open.value = value;
// }

const logout = async () => {
    num.value++;
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: "You want to logout?",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Logout',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#42b883',
        cancelButtonColor: '#d33',
        reverseButtons: false
    })
    if (result.isConfirmed) {
        const [error, data] = await adminAuthService.logout();
        if (error) console.log(error)
        else {
            if (data.success) {
                sessionStorage.removeItem("adminToken");
                Swal.fire({
                    toast: true,
                    position: 'top',
                    showClass: {
                        icon: 'animated heartBeat delay-1s'
                    },
                    icon: 'success',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1000
                }).then(() => {
                    router.push("/admin/login")
                });
            }
        }
    }
}

const yourOrder = () => {
    router.push("/admin/show_profile")
}
</script>

<template>
    <div
        class='flex justify-between items-center w-[95%] bg-white py-3 px-5 lg:px-10 mx-auto rounded-sm shadow-[#E1E1E1_0px_1px_8px]  my-4'>
        <div class="flex items-center gap-3">
            <v-app-bar-nav-icon class="block xl:hidden" variant="text"
                @click.stop="toggleMenu = !toggleMenu; removeRail = false; console.log(removeRail)"></v-app-bar-nav-icon>
            <!-- <img src="" alt=""> -->
            <!-- <i @click="open = !open" class='lg:hidden text-2xl text-gray-600 mr-2 cursor-pointer fas fa-bars'>Click</i> -->
            <!-- <img alt="" class='w-10 h-10 object-cover' />
            <h1 class='font-semibold text-2xl lg:text-title text-gray-800'>Merchant</h1> -->
        </div>
        <button class='flex gap-4 items-center relative group cursor-default w-9 h-9'>
            <img src="" alt="" class='w-9 h-9 rounded-full object-cover cursor-pointer' />
            <div
                class="py-5 px-4 group-focus:block hidden absolute bg-white top-[57px] right-0 z-50 w-[200px] border border-solid border-gray-300 shadow-[#E1E1E1_0px_1px_8px] rounded-md">
                <div class="flex items-center gap-3">
                    <img src=""
                        class='fa fa-user w-9 h-9 bg-[#f3f5f9] rounded-full text-center leading-10 md:leading-[46px] cursor-pointer object-cover' />
                    <div class="flex flex-col text-start">
                        <p class='text-sm font-semibold truncate text-gray-700'>{{ user?.name }}</p>
                        <p class='text-xs font-semibold truncate text-gray-500'>{{ user?.role === 3 ? "admin" :
                            "idk" }}</p>
                    </div>
                </div>
                <div @click="yourOrder()"
                    class="flex gap-2 items-center mt-5 cursor-pointer hover:bg-gray-200 p-2 bg-slate-50 rounded-md">
                    <p class='text-sm font-semibold text-gray-800'>Profile</p>
                    <!-- <i class="fas fa-user text-sm text-gray-700"></i> -->
                </div>
                <!-- <div class="flex gap-2 items-center mt-3 cursor-pointer hover:underline" @click="yourOrder()">
                            <i class="fas fa-shopping-bag text-sm text-gray-700"></i>
                            <p class='text-sm font-semibold text-gray-800'>Orders</p>
                        </div> -->
                <div class="border-t border-solid border-gray-300 mt-7">
                    <div @click="logout()"
                        class="cursor-pointer flex gap-2 items-center justify-center bg-red-500 text-white hover:bg-red-400 border-solid border border-gray-300 rounded-md py-2 mt-3">
                        <!-- <MdLogout class='text-lg font-semibold' /> -->
                        <p class='text-sm font-semibold'>Logout</p>
                    </div>
                </div>

            </div>
        </button>
    </div>
</template>