<script setup lang="ts">
import _ from 'lodash';
import { computed } from '@vue/reactivity';
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import { IOrder, IOrderItem } from '../types/Order';
import { orderService } from '../service/api/modules/order.api'
import { Upload } from '../service/helpers';
import PaginationComponent from '../components/PaginationComponent.vue';

const header = ['NAME', 'QTY', 'TOTAL'];
const ITEM_PER_PAGE = 5;

let orders = ref<Array<IOrderItem[]>>([])
let invoice = ref<number[]>([])
let total = ref<number[]>([])

//paginate
let currentPage = ref<number>(1);
let itemsPerPage = ref<number>(0);
let totalItems = ref<number>(0);
let totalPages = ref<number>(0);
let paginationLoaded = ref<boolean>(false);

//search
let selectedSearchOption = ref("");
let searchQuery = ref("");
let searchOption = ref([
    { id: 1, title: "Invoice", by: "invoice" },
]);

onMounted(async () => {
    selectedSearchOption.value = searchOption.value[0].by;
    getOrders();
    // token.value = sessionStorage.getItem('adminToken') || '';
});

const currentPageUpdated = (value: number): void => {
    if (searchQuery.value !== "") {
        searchOrders(value);
    } else if (currentPage.value !== value) {
        getOrders(value);
    }
};

const setPagination = (pn: number, ipp: number, ti: number, tp: number) => {
    currentPage.value = pn;
    itemsPerPage.value = ipp;
    totalItems.value = ti;
    totalPages.value = tp;
}

const getOrders = async (pageNumber = 1) => {
    const [error, data] = await orderService.getAllOrders(pageNumber)
    if (error) console.log(error)
    else {
        orders.value = data.data;
        invoice.value = data.invoice;
        total.value = data.total;
    }
}

const searchOrders = _.debounce(async (pageNumber = 1) => {
    const params = {
        [selectedSearchOption.value]: searchQuery.value,
        page: pageNumber,
    }
    console.log(params)
    const [error, data] = await orderService.searchOrders(params)
    if (error) console.log(error);
    else {
        if (data.success) {
            console.log(data)
            orders.value = data.data as IOrderItem[][]
            invoice.value = data.invoice;
            total.value = data.total;
            setPagination(pageNumber, data.per_page, data.total_item, data.total_page)
        }
        else {
            getOrders(1);
        }
    }
}, 200)

</script>

<template>
    <div class='lg:py-6 lg:px-8 p-5'>
        <h1 class='text-xl font-bold text-black_500'>Orders</h1>
    </div>

    <div class="flex gap-4 flex-col md:flex-row lg:items-center justify-between px-5 lg:px-8 mb-4">
        <div class="flex gap-3">
            <div class='relative'>
                <input @keyup="searchOrders()" v-model="searchQuery" type="text" name="" id="" placeholder='Search order'
                    class='text-sm pl-10 w-full lg:w-[300px] h-[40px] rounded-md border-gray-300 border-solid border focus:border-current focus:ring-current' />
                <img :src="Upload.icon('search.svg')" alt="" class="absolute top-[50%] left-3 translate-y-[-50%] w-4 h-4" />
            </div>
            <div class="flex flex-row justify-between items-center gap-3">
                <p class='text-sm font-semibold text-gray-800'>by:</p>
                <select name="" id="" class="w-[100px] text-[14px] cursor-pointer" v-model="selectedSearchOption">
                    <option v-for="item in searchOption" :value="item.by" :key="item.id">{{ item.title }}
                    </option>
                </select>
            </div>
        </div>
        <!-- <RouterLink to="/admin/add_product">
            <button class='flex items-center gap-1 px-4 py-3 rounded-md bg-main text-white text-sm cursor-pointer'>
                <img :src="Upload.icon('plus.svg')" alt="" />Add Product
            </button>
        </RouterLink> -->
    </div>

    <div class="lg:py-6 lg:px-8 p-5">
        <div class='bg-white relative p-2'>
            <div v-for="(mainItem, mainIndex) in orders" :key="mainIndex"
                class="w-full pb-2 overflow-x-auto overflow-y-none scroll-smooth scrollbar-thin scrollbar-track-gray-200 scrollbar-track-rounded-xl scrollbar-thumb-current scrollbar-thumb-rounded-xl border-gray-300 shadow-[#E1E1E1_0px_1px_8px] rounded-md mt-5">
                <div v-if="invoice" class="p-3">
                    <h2 class="text-md font-semibold text-gray-600 underline">Invoice #{{ invoice[mainIndex] }}</h2>
                </div>
                <table class='w-[1100px] md:w-full'>
                    <thead class='border-solid border-b border-gray-300'>
                        <tr>
                            <!-- <th class='text-start text-gray-600 text-md w-[50px] py-2 px-3'>
                                <input type="checkbox" name="" id=""
                                    class='w-4 h-4 border-solid border border-gray-500 rounded-[4px] checked:rounded-[4px]' />
                            </th> -->
                            <th v-for="(item, index) in header" :key="index"
                                :class="index == 5 ? 'w-[200px] text-start' : index == 0 ? 'w-[300px] text-center' : 'text-start'"
                                class="text-gray-600 text-sm py-2 px-3">{{ item }}</th>
                            <th class='text-end text-gray-600 text-sm py-2 px-3 pr-4'></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class='group relative  border-solid border-b border-gray-300' v-for="(item, index) in mainItem"
                            :key="index">
                            <!-- <td className='text-sm text-gray-700 py-2 px-3'>
                                <input type="checkbox" name="" id=""
                                    className='w-4 h-4 cursor-pointer border-solid border border-gray-500 rounded-[4px] checked:rounded-[4px]' />
                            </td> -->
                            <td className='py-2 px-3 flex items-center gap-1'>
                                <img :src="Upload.image(item.product?.image)" alt=""
                                    className='w-[45px] h-[45px] border-solid border border-gray-300 rounded-md object-cover' />
                                <p
                                    className='w-[230px] text-ph font-semibold truncate text-primary py-2 px-3 cursor-pointer hover:underline'>
                                    {{ item.product?.name }}</p>
                            </td>
                            <td className='text-ph font-semibold text-gray-700 py-2 px-3'>{{ item.qty
                            }}
                            </td>
                            <td className='text-ph font-semibold text-gray-700 py-2 px-3'>${{ item.unit_price }}</td>
                        </tr>
                    </tbody>
                </table>
                <div v-if="total" class="p-3">
                    <h2 class="text-md font-semibold text-gray-600">Total: ${{ total[mainIndex] }}</h2>
                </div>
            </div>
        </div>
        <div class="flex items-center justify-between border-t border-gray-200 bg-white px-2 py-3 sm:px-2">
            <PaginationComponent v-if="paginationLoaded" :current-page="currentPage" :items-per-page="itemsPerPage"
                :total-items="totalItems" :total-pages="totalPages" @current-page-updated="currentPageUpdated" />
        </div>
    </div>
</template>