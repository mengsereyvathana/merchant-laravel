<script setup lang="ts">
import { computed, ref, defineProps, defineEmits } from 'vue';
import { RouterLink, useRoute } from 'vue-router';
import { Upload } from '../../service/helpers';
import { toggleMenu, removeRail } from '@/admin/store/toggle';

let drawer = ref(toggleMenu)
let rail = ref(removeRail)

const props = defineProps(['openSide']);
const emit = defineEmits(['closeSide']);

/*=========== get route name ===========*/
const routeName = computed(() => {
    let name = useRoute().name;
    if (name == "edit_category") name = "show_category";
    if (name == "edit_product") name = "show_product";
    if (name == "edit_product_scheme") name = "show_product_scheme";
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
        icon: "mdi-view-dashboard",
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

        icon: "mdi-truck",
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
        icon: "mdi-shape",
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
        icon: "mdi-cart",
        hasSub: true
    },
    {
        title: "Slideshows",
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
        icon: "mdi-monitor",
        hasSub: true
    },
];

/*=========== init open sub meu ===========*/
let open = ref([false, false, false, false, false, false, false]);



</script>

<template>
    <v-navigation-drawer v-model="drawer" :rail="rail" @click="rail = false">
        <v-list-item prepend-avatar="../images/merchant-app.png" title="Merchant" nav>
            <template v-slot:append>
                <v-btn class="d-none d-lg-block" variant="text" icon="mdi-chevron-left" @click.stop="rail = !rail"></v-btn>
                <v-btn class="block d-lg-none" variant="text" icon="mdi-close"
                    @click.stop="toggleMenu = !toggleMenu"></v-btn>
            </template>
        </v-list-item>
        <v-list v-model:opened="open" density="compact" nav>
            <div v-for="(item, index) in menus" :key="index">
                <v-list-group v-if="item.hasSub" :value="item.title">
                    <template v-slot:activator="{ props }">
                        <v-list-item v-bind="props" :title="item.title" :prepend-icon="item.icon"></v-list-item>
                    </template>
                    <div v-for="sub in item.children" :key="sub.page_name">
                        <RouterLink :to="'/admin/' + sub.page_name">
                            <v-list-item :title="sub.title" prepend-icon="mdi-arrow-right-bold-circle-outline"
                                :value="sub.title"></v-list-item>
                        </RouterLink>
                    </div>
                </v-list-group>
                <RouterLink v-else :to="'/admin/' + (item.page_name == 'dashboard' ? '' : item.page_name)">
                    <v-list-item :prepend-icon="item.icon" :title="item.title" :value="item.title"
                        class="mb-[4px] mx-0"></v-list-item>
                </RouterLink>
            </div>
        </v-list>
    </v-navigation-drawer>
</template>
