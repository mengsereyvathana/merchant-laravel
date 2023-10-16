<script setup lang="ts">
import { computed, ref, defineProps, defineEmits } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import { Upload } from '../../service/helpers';

const props = defineProps(['openSide']);
const emit = defineEmits(['closeSide']);

/*=========== get route name ===========*/
const routeName = computed(() => {
    let name = useRoute().name;
    if (name == "edit_category") name = "show_category";
    if (name == "edit_product") name = "show_product";
    if (name == "edit_slideshow") name = "show_slideshow";
    if (name == "orderdetail") name = "";
    return name;
});

/*=========== init menu ===========*/
const menus = [
    {
        title: 'Dashboard',
        path: '/admin',
        page_name: 'dashboard',
        icon: "pie-chart.svg",
        hasSub: false
    },
    {
        title: 'Orders',
        children: [
            {
                title: 'Delivered',
                path: '/admin/show_order_delivered',
                page_name: 'show_order_delivered',
            },
            // {
            //     title: 'Pending',
            //     path: '/admin/show_order_pending',
            //     page_name: 'show_order_pending',
            // },
        ],

        icon: "shopping-bag.svg",
        hasSub: true
    },
    {
        title: "Categories",
        children: [
            {
                title: 'Add Category',
                path: '/admin/add_category',
                page_name: 'add_category',
            },
            {
                title: 'Category',
                path: '/admin/show_category',
                page_name: 'show_category',
            }
        ],
        icon: "box.svg",
        hasSub: true
    },
    {
        title: "Products",
        children: [
            {
                title: 'Add Product',
                path: '/admin/add_product',
                page_name: 'add_product',
            },
            {
                title: 'Product',
                path: '/admin/show_product',
                page_name: 'show_product',
            },
            {
                title: 'Product Scheme',
                path: '/admin/show_product_scheme',
                page_name: 'show_product_scheme',
            },
        ],
        icon: "shopping-cart.svg",
        hasSub: true
    },
    {
        title: "Slideshow",
        children: [
            {
                title: 'Add Slideshow',
                path: '/admin/add_slideshow',
                page_name: 'add_slideshow',
            },
            {
                title: 'Slideshow',
                path: '/admin/show_slideshow',
                page_name: 'show_slideshow',
            }
        ],
        icon: "monitor.svg",
        hasSub: true
    },
];

/*=========== init open sub meu ===========*/
let open = ref([false, false, false, false, false, false, false]);

function toggleMenu(index: number) {
    for (let i = 0; i < open.value.length; i++) {
        if (index != i) open.value[i] = false;
    }
    open.value[index] = !open.value[index];
}


function closeSidebar() {
    if (props.openSide) {
        emit('closeSide', false);
    }
}

</script>

<template>
    <div class="overflow-hidden p-[0.75rem] flex flex-col fixed z-10 left-0 w-full lg:w-[250px] bg-white lg:border-r h-[0%] lg:h-[100%] lg:overflow-y-auto overflow-x-hidden transition-all border-t-0 shadow-[#E1E1E1_0px_0px_8px] gap-1"
        :class="openSide ? 'h-[90%]' : 'py-[0rem] lg:p-[0.75rem] h-[0%]'">
        <div v-for="(item, index) in menus" :key="index">
            <div v-if="item.hasSub" class="flex flex-col gap-1">
                <div @click="toggleMenu(index)"
                    class="flex justify-between items-center cursor-pointer py-[12px] px-3 rounded-md hover:bg-hover_menu">
                    <div class='flex items-center'>
                        <h1 class='mr-2'><img class="w-4 h-4" :src="Upload.icon(item.icon)" alt=""></h1>
                        <p :class="item.children?.some(p => p.page_name == routeName) && !open[index] ? 'text-primary' : ''"
                            class="text-side font-semibold">{{ item.title }}</p>
                    </div>
                    <div>
                        <img :src="Upload.icon('right.svg')" alt="" :class="open[index] ? 'rotate-90' : 'rotate-0'">
                    </div>
                    <!-- <i class="mr-1 text-sm font-extrabold fa-solid fa-angle-right"></i> -->
                </div>
                <div class="flex flex-col gap-1" v-if="open[index]">
                    <div v-for="sub in item.children" :key="sub.page_name">
                        <RouterLink :to="'/admin/' + sub.page_name">
                            <div class="py-[12px] px-3 cursor-pointer rounded-md hover:bg-hover_menu"
                                :class="routeName == sub.page_name ? 'text-primary bg-active_menu' : ''"
                                @click="closeSidebar">
                                <p class="text-side font-semibold pl-12">{{ sub.title }}</p>
                            </div>
                        </RouterLink>
                    </div>
                </div>
            </div>
            <div v-else>
                <RouterLink :to="'/admin/' + (item.page_name == 'dashboard' ? '' : item.page_name)">
                    <div class='flex items-center py-[12px] px-3 rounded-md hover:bg-hover_menu'
                        :class="routeName == item.page_name ? 'text-primary lg:bg-active_menu' : ''" @click="closeSidebar">
                        <h1 class='mr-2'><img class="w-4 h-4" :src="Upload.icon(item.icon)" alt=""></h1>
                        <p class=" text-side font-semibold">{{
                            item.title
                        }}
                        </p>
                    </div>
                </RouterLink>
            </div>
        </div>
    </div>
</template>
