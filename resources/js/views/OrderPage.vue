<script setup lang="ts">
import { onMounted, ref } from 'vue';
import { Upload, UserID } from '@/services/helper/index';
import { IOrderItem } from '@/types/Order';
import { orderService } from '@/services/api/modules/order.api';
import { store } from '@/store';
import { AUTH_STORE } from '@/store/constants';

const order_loaded = ref<boolean>(false);
const invoice = ref<number[]>([]);
const total = ref<number[]>([]);
const order_item = ref<Array<IOrderItem[]>>([]);

onMounted(() => {
    getProductOrder();
})

const getProductOrder = async () => {
    const [response] = await orderService.getAllOrders(UserID.getUser());
    if (response.success) {
        order_item.value = response.data.reverse();
        invoice.value = response.invoice.reverse();
        total.value = response.total.reverse();
        order_loaded.value = true;
    } else {
        order_loaded.value = true;
    }
}

</script>
<template>
    <div v-if="order_loaded && order_item.length > 0" class="flex flex-col p-4 gap-3 bg-white border-l border-solid">
        <div v-for="(item, index) in order_item" class="bg-white shadow-[0_8px_30px_rgb(0,0,0,0.12)] rounded-lg"
            :key="index">
            <div class="p-4">
                <h3 class="border-b border-solid border-gray-200 uppercase pb-4 font-semibold">Invoice #{{ invoice[index] }}
                </h3>
            </div>
            <div class="overflow-x-auto scrollbar-thin">
                <div v-for="(items, indexs) in item" class="flex justify-between p-4 first:pt-0 gap-2" :key="indexs">
                    <div v-if="items.product" class="flex gap-3">
                        <div class="min-w-[4rem] h-[4rem] w-[4rem]">
                            <img :src="Upload.image(items.product.image)"
                                class="w-full h-full bg-[#f3f5f9] rounded text-center leading-10 cursor-pointer object-cover" />
                        </div>
                        <div class="flex flex-col">
                            <h1 class="text-md text-dark text-gray-600 font-semibold">
                                {{ items.product.name }}
                            </h1>
                            <p class="text-xs text-letter mt-1 font-semibold">
                                <span>${{ items.unit_price }}
                                </span>
                            </p>
                            <!-- <p class="text-xs text-gray-700">{{ items.product.description }}</p> -->
                        </div>
                    </div>
                    <div class="flex flex-col justify-between items-end text-xs font-medium">
                        <p class="border border-solid rounded-full p-2">x{{ items.qty }}</p>
                        <p class="text-danger">${{ items.total }}</p>
                    </div>
                </div>
            </div>
            <div class="p-4">
                <div class="flex justify-between border-t border-solid border-gray-200 w-full">
                    <h3 class="uppercase pt-4 font-semibold">Total</h3>
                    <h3 class="uppercase pt-4 font-semibold text-danger">${{ total[index] }}</h3>
                </div>
            </div>
        </div>
    </div>
    <div v-else-if="order_loaded && order_item.length === 0"
        class="mycontainer h-[75vh] flex justify-center items-center flex-col">
        <h1 class="text-lg md:text-4xl font-semibold mt-10">Order is <span class="text-main">Empty!</span></h1>
    </div>
</template>
