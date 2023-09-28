<script setup lang="ts">
import { Crypt, Upload, UserID } from '@/services/helper/index';
import { onMounted, ref, watch } from 'vue';
import { RouterLink } from 'vue-router';
import { IProductItem } from '../types/Product'
import { useStore } from '@/use/useStore';
import { CART_STORE, SEARCH_STORE } from '@/store/constants';
import { productService } from '@/services/api/modules/product.api';

const store = useStore();
const products = ref<IProductItem[]>([]);
const product_loaded = ref<boolean>(false)

watch(() => store.getters[SEARCH_STORE.GETTERS.GET_PRODUCT], (searchedProducts) => {
    products.value = searchedProducts;
})

onMounted(() => {
    getProduct();
})

const getProduct = async () => {
    const [error, data] = await productService.getAllProducts(UserID.getUser());
    if (error) console.log(error);
    else {
        if (data.success) {
            products.value = data.data
            product_loaded.value = true
        }
    }
}

const addToCart = async (product_id: number) => {
    await store.dispatch(CART_STORE.ACTIONS.ADD_TO_CART, { product_id })
};

const idDetail = (id: number): string | null => {
    return Crypt.encrypt(id.toString())
}
</script>
<template>
    <div v-if="products.length > 0" class="mt-1 p-4">
        <!-- <RouterLink to="/cart" class="ripple-effect bg-white w-[100px] h-[100px]" v-ripple-init>Hello world</RouterLink> -->
        <div class="flex justify-between items-end">
            <div class="flex items-center">
                <img :src="Upload.icon('trending.svg')" alt="" class='w-[20px] h-[20px] mr-1' />
                <span class='min-[320px]:text-sm text-2xl font-[700] text-gray-800 capitalize'>Trending</span>
            </div>
            <!-- <p @click="more = !more" class="text-sm hover:underline cursor-pointer text-primary">
                {{ more ? 'See less' :
                'See more' }}
            </p> -->
        </div>
        <div class="grid min-[320px]:grid-cols-2 min-[320px]:gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-9 mt-5">
            <div v-for="item in products" :key="item.id">
                <div
                    class="bg-white cursor-pointer relative rounded-[5px] shadow-[rgba(50,_50,_105,_0.15)_0px_2px_5px_0px,_rgba(0,_0,_0,_0.05)_0px_1px_1px_0px] overflow-hidden group">
                    <div class="">
                        <RouterLink :to="'/product_detail/' + idDetail(item.products.id)">
                            <img :src="Upload.image(item.products.image)" alt='' class='w-full h-[150px] object-cover' />
                        </RouterLink>
                    </div>
                    <div class="p-2">
                        <h3 class='text-md mb-2 cursor-pointer truncate text-lettermain font-semibold'>{{ item.products.name
                        }}</h3>
                        <div class="flex justify-between items-end">
                            <div class="flex flex-col gap-1 font-normal">
                                <div class="flex items-center gap-2 text-sm text-letter">
                                    <span class="">Buy: </span>
                                    <h4 class=''>${{ item.unit_price.toFixed(2) }}</h4>
                                </div>
                                <div class="flex items-center gap-2 text-sm">
                                    <span class=" text-letter">Sell: </span>
                                    <h4 class=' text-danger'>${{ item.products.price.toFixed(2) }}</h4>
                                </div>
                                <div class="flex items-center gap-2 text-sm">
                                    <span class=" text-letter">Margin: </span>
                                    <h4 class=' text-danger'>${{ item.margin.toFixed(2) }}</h4>
                                </div>
                            </div>
                            <button @click="addToCart(item.products.id)" type='button'
                                class='text w-[30px] h-[30px] border border-solid border-main rounded-full cursor-pointer transition duration-500 hover:bg-gray-50 ripple-effect'
                                v-ripple-init>
                                <img :src="Upload.icon('plus.svg')" class='w-[15px] h-[15px] m-auto' />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="h-screen flex mt-10 justify-center" v-else>
        <v-progress-circular indeterminate color="main"></v-progress-circular>
    </div>
</template>