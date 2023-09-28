<script setup lang="ts">
import { ref, onMounted, onBeforeUnmount } from 'vue';
import { useRoute } from 'vue-router';
import { Upload, UserID } from '@/services/helper/index';
import { IProductDetailItem } from '@/types/ProductDetail';
import { useStore } from '@/use/useStore';
import { CART_STORE } from '@/store/constants';
import { productService } from '@/services/api/modules/product.api';

const store = useStore();
const route = useRoute();

let product_detail = ref<Partial<IProductDetailItem>>({});
let product_loaded = ref(false);

const getProductDetail = async (): Promise<void> => {
    const product_id = route.params.id as string;
    try {
        const [error, data] = await productService.getProductDetail(UserID.getUser(), product_id)
        if (error) console.log(error)
        else {
            if (data.success) {
                product_detail.value = data.data;
                product_loaded.value = true;
            }
        }
    } catch (error) {
        console.log(error)
    }
};

onMounted(async () => {
    await getProductDetail();
});
onBeforeUnmount(() => {
    product_loaded.value = false;
});
const addToCart = async (product_id: number | undefined) => {
    await store.dispatch(CART_STORE.ACTIONS.ADD_TO_CART, { product_id })
};

</script>

<template>
    <div v-if="product_loaded" class="bg-white">
        <p class="mycontainer py-0 mt-4 text-xs text-main">
            <button>
                <span class="hover:underline font-bold cursor-pointer">{{ product_detail.products?.category?.name }} </span>
            </button>
            <span class="text-gray-700"> > </span>
            <span class="font-semibold"> {{ product_detail.products?.name }}</span>
        </p>
        <div class="mycontainer flex flex-col lg:flex-row gap-5 lg:gap-0">
            <div class="flex flex-col gap-5 w-full">
                <div class="flex flex-col lg:flex-row gap-5 w-full">
                    <img :src="Upload.image(product_detail?.products?.image)" alt=""
                        class="w-full lg:w-[450px] h-[200px] object-cover rounded-lg border border-solid border-gray-300 cursor-pointer" />
                </div>
            </div>
            <div class="w-full flex flex-col gap-5">
                <h1 class="w-full text-xl lg:text-3xl font-semibold text-ellipsis text-lettermain">
                    {{ product_detail.products?.name }}
                </h1>
                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-2 text-sm">
                        <span class="font-semibold text-letter">Buy: </span>
                        <h4 class=' text-base font-semibold text-gray-600'>${{ product_detail?.products?.buy }}</h4>
                    </div>
                    <div class="flex items-center gap-2 text-sm ">
                        <span class="font-semibold text-letter">Sell: </span>
                        <h4 class='text-base font-semibold text-danger'>${{ product_detail?.products?.price }}</h4>
                    </div>
                    <div class="flex items-center gap-2 text-sm">
                        <span class="font-semibold text-letter">Margin: </span>
                        <h4 class='text-base font-semibold text-danger'>${{ product_detail?.products?.price }}</h4>
                    </div>
                </div>
                <button v-if="product_detail.products"
                    class="text-sm bg-main text-white py-[2px] w-[90px] rounded-xl capitalize">
                    {{ product_detail.products?.stock > 0 ? 'In stock' : 'Out Stock' }}
                </button>
                <p class="text-gray-600">Description: {{ product_detail.products?.description }}</p>
                <div class="flex justify-between items-center w-full lg:w-[550px] gap-5">
                    <button @click="addToCart(product_detail.products?.id)" v-ripple-init
                        class="ripple-effect flex justify-center items-center gap-2 w-full text-sm font-semibold bg-main text-white rounded-full border border-solid border-primary py-3 hover:opacity-95">
                        <i class="fas fa-shopping-cart"></i>
                        <p>Add to cart</p>
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
