<script setup lang="ts">
import { ref, onMounted, watch, computed } from "vue";
import { useRoute, useRouter } from 'vue-router';
import { Upload } from "@/services/helper/index";
import { transition } from '@/store/transition'
import { useStore } from "@/use/useStore";
import { CART_STORE } from "@/store/constants";

const store = useStore();

const route = useRoute();
const router = useRouter();
const previousRouteName = ref<string>("");

const page_name = ref<string>('');

const page_off = [
    "category"
]

interface PageAcross {
    [key: string]: string;
}
const page_across: PageAcross[] = [
    { product_detail: "/", },
    { product_category: "/category", }
]

watch(() => route.name, (to, previous) => {
    previousRouteName.value = previous as string;
})

onMounted(() => {
    getProductCart();
    setPageName(route.path);
});

watch(() => route.path, (newName) => {
    setPageName(newName);
});

const setPageName = (name: string) => {
    page_name.value = name.replace("_", " ").substring(1);
    let new_page: string[] = page_name.value.split('/');
    if (new_page.length >= 2) {
        new_page.splice(-1, 1);
        page_name.value = new_page.join("/");
    }
}
const isAcrossPage = () => {
    const current_page = route.name;
    for (let i = 0; i < page_across.length; i++) {
        if (page_across[i][current_page as keyof PageAcross]) {
            return true
        }
    }
    return false
}

let isPageOff = computed<boolean>(() => {
    return page_off.includes(page_name.value);
})

function back() {
    let all_path: string[] = route.path.split('/');
    let new_path: string;
    if (all_path.length > 2) {
        if (isAcrossPage()) {
            const current_page = route.name;
            for (let i = 0; i < page_across.length; i++) {
                if (page_across[i][current_page as keyof PageAcross]) {
                    if (previousRouteName.value === "product_category") {
                        transition.value = "slide-right";
                        router.go(-1);
                    } else {
                        router.push(Object.values(page_across[i])[0]);
                    }
                }
            }
        } else {
            all_path.splice(-1, 1);
            new_path = all_path.join('/');
            router.push(new_path);
        }
    } else {
        all_path = [];
        router.push("/");
    }
}

let getCartQty = computed<number>(() => {
    return store.getters[CART_STORE.GETTERS.GET_QTY]
})

const getProductCart = async () => {
    await store.dispatch(CART_STORE.ACTIONS.GET_CART_ITEM_API, {})
}
</script>
<template>
    <div class="fixed top-0 left-0  h-[60px] z-20  flex items-center w-full bg-white"
        :class="page_name == '' ? 'justify-end' : 'justify-between'">
        <div v-if="page_name != ''" class="flex">
            <button @click="back()" class="ripple-effect" v-ripple-init>
                <div class="w-[60px] h-[60px] p-3 cursor-pointer flex justify-center items-center">
                    <img :src="Upload.icon('back.svg')" alt="" class="w-[20px] p-[3px]" />
                </div>
            </button>
            <div class="p-3 cursor-pointer flex justify-center items-center">
                <h3 class="uppercase font-medium">{{ isPageOff ? '' : page_name }}</h3>
            </div>
        </div>
        <div v-else class="basis-1/2">
            <h3 class="text-md text-gray-700 font-semibold ml-2">Merchant App</h3>
        </div>
        <div class="flex items-center gap-1">
            <RouterLink to="/" v-if="page_name != ''" class="ripple-effect" v-ripple-init>
                <div class="w-[60px] h-[60px] cursor-pointer flex justify-center items-center transition-all">
                    <img :src="Upload.icon('home.svg')" alt="" class="w-[20px] h-[20px]" />
                </div>
            </RouterLink>
            <RouterLink to="/cart" v-if="page_name != 'cart'" class="ripple-effect" v-ripple-init>
                <div class="w-[60px] h-[60px] cursor-pointer flex justify-center items-center relative ">
                    <img :src="Upload.icon('cart.svg')" alt="" class="w-[20px] h-[20px]" />
                    <span v-if="getCartQty > 0"
                        class="absolute top-3 right-2.5 w-4 h-4 md:w-5 md:h-5 leading-4 md:leading-5 rounded-full text-center bg-main text-[13px] text-white">
                        {{ getCartQty }}
                    </span>
                </div>
            </RouterLink>
        </div>
    </div>
</template>