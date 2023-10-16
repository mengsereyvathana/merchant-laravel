<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Upload } from '../service/helpers';
import { adminAuthService } from '../service/api/modules/auth-admin.api';
import { IUserDataItem } from '../types/IUserData';

let user = ref<IUserDataItem>();

onMounted(() => {
    getUser();
})

const getUser = async () => {
    const [error, data] = await adminAuthService.getUser();
    if (error) console.log(error)
    else {
        if (data.success) {
            console.log(data.data)
            user.value = data.data;
        }
    }
}

const editProfile = async () => {
    const [error, data] = await adminAuthService.getUser();
    if (error) console.log(error)
    else {
        if (data.success) {
            console.log(data.data)
            user.value = data.data;
        }
    }
}

</script>

<template>
    <div class="lg:py-3 lg:px-8 p-3">
        <div class='flex justify-between items-center mb-4'>
            <h1 class='text-header font-bold text-gray-600'>Profile</h1>
            <button @click="editProfile()" class='px-4 py-3 rounded-md bg-primary text-white text-sm cursor-pointer'>Edit
                Profile</button>
        </div>
        <div
            class='relative overflow-hidden border-solid border-b border-gray-200 p-3 shadow-[#E1E1E1_0px_1px_8px] rounded-md'>
            <div v-if="user"
                class="w-full px-3 py-3 overflow-x-auto overflow-y-none scroll-smooth scrollbar-thin scrollbar-track-gray-200 scrollbar-track-rounded-xl scrollbar-thumb-current scrollbar-thumb-rounded-xl ">
                <div class="flex flex-row items-center justify-between">
                    <div class="w-36 h-36">
                        <img :src="user.image"
                            class='w-36 h-36 bg-[#f3f5f9] rounded-full text-center leading-10 md:leading-[46px] cursor-pointer object-cover' />
                    </div>
                    <div class="grid grid-cols-3 gap-4 items-center">
                        <div class="mt-1 w-56">
                            <div class="flex justify-between">
                                <h1 class='text-sm font-semibold text-gray-800'>Username</h1>
                            </div>
                            <input type="text" name="" id="" class='input text-sm w-full' placeholder='Write name here...'
                                v-model="user.name" />
                        </div>
                        <div class="mt-1 w-56">
                            <div class="flex justify-between">
                                <h1 class='text-sm font-semibold text-gray-800'>Phone</h1>
                            </div>
                            <input type="text" name="" id="" class='input text-sm w-full' placeholder='Write name here...'
                                v-model="user.phone" />
                        </div>
                        <div class="mt-1 w-56">
                            <div class="flex justify-between">
                                <h1 class='text-sm font-semibold text-gray-800'>Email</h1>
                            </div>
                            <input type="text" name="" id="" class='input text-sm w-full' placeholder='Write name here...'
                                v-model="user.email" />
                        </div>
                        <!-- <div class="mt-1">
                            <div class="flex justify-between">
                                <h1 class='text-sm font-semibold text-gray-800'>Username</h1>
                            </div>
                            <input type="text" name="" id="" class='input text-sm w-full' placeholder='Write name here...'
                                v-model="form.name" />
                        </div> -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>