<script setup lang="ts">
import { ref, onMounted } from "vue";
import { RouterLink } from "vue-router";
import { Upload } from "@/services/helper/index";
import { getAuth, onAuthStateChanged } from "firebase/auth";
import { useStore } from "@/use/useStore";
import { AUTH_STORE } from "@/store/constants";
import { userService } from "@/services/api/modules/user.api";

const store = useStore();

interface Menu {
    title: string;
    path: string;
    page_name: string;
    icon: string;
}

const menu: Menu[] = [
    {
        title: "Home",
        path: "/",
        page_name: "home",
        icon: "home.svg",
    },
    {
        title: "Category",
        path: "/category",
        page_name: "category",
        icon: "category.svg",
    },
    {
        title: "Order",
        path: "/my_order",
        page_name: "order",
        icon: "order.svg",
    },
    {
        title: "Contact",
        path: "/contact",
        page_name: "contact",
        icon: "contact.svg",
    },
    {
        title: "Account",
        path: "/my_account",
        page_name: "my_account",
        icon: "user.svg",
    },
];

type Auth = ReturnType<typeof getAuth>;
let auth: Auth;

const isLoggedIn = ref<boolean>(false);
const token = ref<string | null>("");
let nav_loaded = ref<boolean>(false);

onMounted(() => {
    auth = getAuth();
    onAuthStateChanged(auth, (user) => {
        if (user) {
            isLoggedIn.value = true;
        }
    });
});

// const checkAuth = async () => {
//     token.value = store.getters[AUTH_STORE.GETTERS.GET_TOKEN]
//     const response = await userService.checkAuth(token.value);
//     if (response.success) {
//         nav_loaded.value = true
//     }
// }
</script>
<template>
    <div
        class="fixed z-10 flex flex-col item-center bottom-0 left-0 right-0 w-full bg-white shadow-lg first:border-t-0 rounded-lg">
        <div class="bg-white rounded-md shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-solid border-gray-50">
            <div class="flex justify-between items-center">
                <div v-for="item in menu" :key="item.title" class="w-full h-full">
                    <RouterLink :to="item.path">
                        <div class="cursor-pointer flex flex-col justify-center items-center p-2 ripple-effect"
                            v-ripple-init>
                            <img :src="Upload.icon(item.icon)" alt="" class="w-[25px] h-[25px]" />
                            <span class="text-sm font-medium">{{
                                item.title
                            }}</span>
                        </div>
                    </RouterLink>
                    <!-- <RouterLink v-else-if="isLoggedIn && item.page_name == 'my_account'" :to="item.path">
                        <div class="cursor-pointer flex flex-col justify-center items-center p-2 ripple-effect"
                            v-ripple-init>
                            <img :src="Upload.icon(item.icon)" alt="" class="w-[25px] h-[25px]" />
                            <span class="text-sm font-medium">{{ item.title }}</span>
                        </div>
                    </RouterLink> -->
                    <!-- <RouterLink v-else :to="'/phone_login'">
                        <div class="cursor-pointer flex flex-col justify-center items-center  p-2 ripple-effect"
                            v-ripple-init>
                            <img :src="Upload.icon(item.icon)" alt="" class="w-[25px] h-[25px] " />
                            <span class="text-sm font-medium">{{ item.title }}</span>
                        </div>
                    </RouterLink> -->
                </div>
            </div>
        </div>
    </div>
</template>
