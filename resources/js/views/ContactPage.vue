<script setup lang="ts">
import { ref } from 'vue';
import { useRouter } from 'vue-router';
import { Upload } from '@/services/helper/index';

const router = useRouter();

const contactMenu = [
    {
        title: '096 818 4733',
        description: 'Call our core center now',
        action: 'phone',
        icon: "phone.svg",
    },
    {
        title: 'Telegram',
        description: 'Telegram our core center now',
        action: 'telegram',
        icon: "telegram.svg",
    },
    {
        title: 'Messenger',
        description: 'Messenger our core center now',
        action: 'messenger',
        icon: "messenger.svg",
    },
    {
        title: 'Map',
        description: 'Our location',
        action: 'map',
        icon: "map.svg",
    },
]

const phoneNumber = ref('+855968184733');
const messenger = ref('https://m.me/kawaiiiUwU')
const telegram = ref('https://t.me/Meng_Sereyvathana')
const map = ref('/map')

function makeAction(action: string) {
    switch (action) {
        case 'phone':
            window.location.href = `tel:${phoneNumber.value}`;
            break;
        case 'telegram':
            window.location.href = telegram.value;
            break;
        case 'messenger':
            window.location.href = messenger.value;
            break;
        case 'map':
            router.push(map.value);
            break;
    }
}
</script>

<template>
    <div class="flex flex-col gap-3 bg-white">
        <div class="flex flex-col gap-4 p-4 mb-[58px]">
            <!-- <div class="capitalize">
                <h3 class="text-gray-700 font-semibold">Contact us</h3>
            </div> -->
            <div>
                <button
                    class="flex flex-row justify-between items-center bg-white shadow-sm p-4 rounded-lg w-full border border-solid mt-4 first:mt-0"
                    v-for="(item, index) in contactMenu" @click="makeAction(item.action)" :key="index">
                    <div class="flex flex-row items-center gap-4">
                        <div class="w-[44px] h-[44px] flex items-center justify-center">
                            <img :src="Upload.icon(item.icon)" alt="" class="w-[30px] h-[30px]">
                        </div>
                        <div class="text-start">
                            <h3 class="text-md font-semibold">{{ item.title }}</h3>
                            <p class="text-xs">{{ item.description }}</p>
                        </div>
                    </div>
                    <div v-if="item.action != 'map'" class="w-[25px] h-[25px] flex items-center justify-center">
                        <img :src="Upload.icon('forward.svg')" alt="" class="w-[22px]">
                    </div>
                    <div v-else-if="item.action == 'map'" class="w-[25px] h-[25px] flex items-center justify-center">
                        <img :src="Upload.icon('back.svg')" alt="" class="w-[15px] h-[15px] rotate-180">
                    </div>
                </button>
            </div>
        </div>
    </div>
</template>