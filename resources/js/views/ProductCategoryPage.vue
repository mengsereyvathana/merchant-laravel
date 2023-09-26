<script setup lang="ts">
import { Upload, Crypt, UserID } from '@/services/helper/index';
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import { IProductItem } from '../types/Product'
import { useStore } from '@/use/useStore';
import { CART_STORE } from '@/store/constants';
import { productService } from '@/services/api/modules/product.api';
import { useRoute } from 'vue-router';

// type TProductCategory = NonNullable<Awaited<ReturnType<typeof productService.getProductByCategory>>>

const route = useRoute();
const store = useStore();

const category_name = ref<string>("");
const products = ref<IProductItem[]>([]);
const product_loaded = ref<boolean>(false)

onMounted(() => {
    getProduct();
})
const getProduct = async () => {
    const response = await productService.getProductByCategory(UserID.getUser(), route.params.id as string)
    if (response.success) {
        category_name.value = response.category_name;
        products.value = response.data;
        product_loaded.value = true;
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
    <div v-if="products.length > 0" class="mycontainer mt-1 p-4">
        <div class="flex justify-between items-end">
            <p class="text-xs text-main">
                <RouterLink :to="''">
                    <span class="hover:underline font-bold cursor-pointer">{{ category_name }}
                    </span>
                </RouterLink>
            </p>
        </div>
        <div class="grid min-[320px]:grid-cols-2 min-[320px]:gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-9 mt-5">
            <div v-for="item in products" :key="item.id">
                <div
                    class="bg-white cursor-pointer relative rounded-[5px] shadow-[0_1px_3px_rgb(3,0,71,0.09)] overflow-hidden group">
                    <div class="">
                        <RouterLink :to="'/product_detail/' + idDetail(item.products.id)">
                            <img :src="Upload.image(item.products.image)" alt='' class='w-full h-[150px] object-cover' />
                        </RouterLink>
                    </div>
                    <div class="p-2">
                        <h3 class='text-md mb-2 cursor-pointer truncate text-lettermain font-semibold'>{{ item.products.name
                        }}</h3>
                        <div class="flex justify-between items-center">
                            <div class="flex flex-col gap-2">
                                <div class="flex items-center gap-2 text-sm text-letter">
                                    <span class="font-semibold">Buy: </span>
                                    <h4 class=' text-base font-semibold'>${{ item.products.buy }}</h4>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-danger">
                                    <span class="font-semibold">Sell: </span>
                                    <h4 class='text-base font-semibold'>${{ item.products.price }}</h4>
                                </div>
                            </div>
                            <button @click="addToCart(item.products.id)" type='button'
                                class='text w-[30px] p-[2px] border border-solid border-primary rounded  cursor-pointer transition duration-500 hover:bg-gray-50'>
                                <img :src="Upload.icon('plus.svg')" class='fa fa-plus w-full text-black text-sm' />
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>