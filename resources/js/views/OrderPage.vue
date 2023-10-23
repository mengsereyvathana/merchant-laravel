<script setup lang="ts">
import PaginationComponent from '@/admin/components/PaginationComponent.vue';
import { onMounted, ref } from 'vue';
import { Upload, UserID } from '@/services/helper/index';
import { IOrderItem } from '@/types/Order';
import { orderService } from '@/services/api/modules/order.api';

const order_loaded = ref<boolean>(false);
const invoice = ref<number[]>([]);
const total = ref<number[]>([]);
const invoiceDate = ref<string[]>([]);
const order_item = ref<Array<IOrderItem[]>>([]);

//paginate
let currentPage = ref<number>(1);
let itemsPerPage = ref<number>(0);
let totalItems = ref<number>(0);
let totalPages = ref<number>(0);
let paginationLoaded = ref<boolean>(false);

onMounted(() => {
    getProductOrder();
})

const setPagination = (pn: number, ipp: number, ti: number, tp: number) => {
    currentPage.value = pn;
    itemsPerPage.value = ipp;
    totalItems.value = ti;
    totalPages.value = tp;
}

const currentPageUpdated = (value: number): void => {
    getProductOrder(value);
};

const getProductOrder = async (pageNumber = 1) => {
    const [error, data] = await orderService.getAllOrders(UserID.getUser(), pageNumber);
    if (error) console.log(error);
    else {
        if (data.success) {
            order_item.value = data.data;
            invoice.value = data.invoice;
            total.value = data.total;
            invoiceDate.value = data.invoice_date;
            setPagination(pageNumber, data.per_page, data.total_invocie, data.total_page)
            paginationLoaded.value = true;
        }
        order_loaded.value = true;
    }
}

</script>
<template>
    <div v-if="order_loaded && order_item.length > 0" class="flex flex-col p-4 gap-3 bg-white border-l border-solid">
        <div v-for="(item, index) in order_item" class="bg-white shadow-[0_8px_30px_rgb(0,0,0,0.12)] rounded-lg"
            :key="index">
            <div class="flex justify-between border-b border-solid border-gray-200">
                <div class="p-4">
                    <h3 class="uppercase pb-4 font-semibold">Invoice #{{
                        invoice[index] }}
                    </h3>
                </div>
                <div class="p-4">
                    <h3 class="uppercase pb-4 font-semibold">Invoice Date #{{
                        invoiceDate[index] }}
                    </h3>
                </div>
            </div>
            <div class="overflow-x-auto scrollbar-thin">
                <div v-for="(items, indexs) in item" class="flex justify-between p-4  gap-2" :key="indexs">
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
                        <p class="p-2">x{{ items.qty }}</p>
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
        <PaginationComponent v-if="paginationLoaded" :current-page="currentPage" :items-per-page="itemsPerPage"
            :total-items="totalItems" :total-pages="totalPages" @current-page-updated="currentPageUpdated" />
    </div>
    <div v-else-if="order_loaded && order_item.length === 0"
        class="mycontainer h-[75vh] flex justify-center items-center flex-col">
        <h1 class="text-lg md:text-4xl font-semibold mt-10">Order is <span class="text-main">Empty!</span></h1>
    </div>
</template>
