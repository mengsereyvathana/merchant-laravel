<script setup lang="ts">
import _ from 'lodash';
import { computed } from '@vue/reactivity';
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import { IOrder, IOrderItem } from '../types/Order';
import { orderService } from '../service/api/modules/order.api'
import { Upload } from '../service/helpers';
import PaginationComponent from '../components/PaginationComponent.vue';
import SearchComponent from '../components/SearchComponent.vue';

const header = ['NAME', 'QTY', 'TOTAL'];

let orders = ref<Array<IOrderItem[]>>([])
let invoice = ref<number[]>([])
let total = ref<number[]>([])
let customerName = ref<string[]>([])
let invoiceDate = ref<string[]>([])

//paginate
let currentPage = ref<number>(1);
let itemsPerPage = ref<number>(0);
let totalItems = ref<number>(0);
let totalPages = ref<number>(0);
let loading = ref<boolean>(false);

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

const currentSearchUpdated = async (value: string, selectOptions: string) => {
    selectedSearchOption.value = selectOptions;
    searchQuery.value = value;
    await search();
}

const currentPageUpdated = (value: number): void => {
    if (searchQuery.value !== "") {
        search(value);
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
        customerName.value = data.buy_by;
        invoiceDate.value = data.invoice_date;
        setPagination(pageNumber, data.per_page, data.total_invoice, data.total_page)
        loading.value = true;
    }
}

const search = _.debounce(async (pageNumber = 1) => {
    const params = {
        [selectedSearchOption.value]: searchQuery.value,
        page: pageNumber,
    }
    const [error, data] = await orderService.searchOrders(params)
    if (error) console.log(error);
    else {
        if (data.success) {
            orders.value = data.data as IOrderItem[][]
            invoice.value = data.invoice;
            total.value = data.total;
            customerName.value = data.buy_by;
            invoiceDate.value = data.invoice_date;
            setPagination(pageNumber, data.per_page, data.total_invoice, data.total_page)
        }
        else {
            getOrders(1);
        }
    }
}, 200)

</script>

<template>
    <div>
        <div class='d-flex flex-row justify-space-between align-center flex-wrap mb-4'>
            <h1 class='text-header text-grey-darken-2 font-weight-medium'>Order</h1>
        </div>
        <v-layout>
            <v-responsive>
                <v-row>
                    <v-col>
                        <SearchComponent :search-option="searchOption" @current-search-updated="currentSearchUpdated" />
                    </v-col>
                </v-row>
                <v-row>
                    <v-col>
                        <div class='bg-white relative'>
                            <div v-for="(mainItem, mainIndex) in orders" :key="mainIndex"
                                class="w-full overflow-x-auto overflow-y-none mt-5 border-2 border-solid border-blue-200 rounded-md">
                                <div class="d-flex justify-space-between flex-sm-row flex-column">
                                    <div v-if="invoice" class="p-3">
                                        <h2 class="font-weight-medium text-grey-darken-2">Invoice #{{
                                            invoice[mainIndex]
                                        }}</h2>
                                    </div>
                                    <div v-if="invoiceDate" class="p-3">
                                        <h2 class="font-weight-medium text-grey-darken-2">Invoice Date #{{
                                            invoiceDate[mainIndex]
                                        }}</h2>
                                    </div>
                                    <div v-if="customerName" class="p-3">
                                        <h2 class="font-weight-medium text-grey-darken-2">Customer name: {{
                                            customerName[mainIndex]
                                        }}</h2>
                                    </div>
                                </div>
                                <v-table class='w-[1100px] md:w-full'>
                                    <thead>
                                        <tr>
                                            <th v-for="(item, index) in header" :key="index"
                                                :class="index == 5 ? 'w-[200px] text-start' : index == 0 ? 'w-[300px] text-center' : 'text-start'"
                                                class="text-grey-darken-2 py-2 px-3">{{ item }}</th>
                                            <th class='text-grey-darken-2 py-2 px-3 pr-4'></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr class='group relative border-solid border-b border-gray-300'
                                            v-for="(item, index) in mainItem" :key="index">
                                            <td v-if="item.product">
                                                <div class="d-flex flex-row align-center">
                                                    <v-img :src="Upload.image(item.product.image)" alt="" aspect-ratio="1/1"
                                                        class='rounded-md mr-3' cover :max-width="50" :width="50"
                                                        :height="50"></v-img>
                                                    <v-hover>
                                                        <template v-slot:default="{ isHovering, props }">
                                                            <span v-bind="props" class="text-grey-darken-3"
                                                                :class="isHovering ? 'text-grey-darken-4' : ''">{{
                                                                    item.product.name
                                                                }}</span>
                                                        </template>
                                                    </v-hover>
                                                </div>
                                            </td>
                                            <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{ item.qty }}</td>
                                            <td class='py-2 px-3 text-body-2 text-grey-darken-3'>${{ item.unit_price }}</td>
                                        </tr>
                                        <tr>
                                            <td class='py-2 px-3 text-body-2 text-grey-darken-3'>Total</td>
                                            <td class='py-2 px-3 text-body-2 text-grey-darken-3'></td>
                                            <td class='py-2 px-3 text-body-2 text-grey-darken-3'>${{ total[mainIndex]
                                            }}</td>
                                        </tr>
                                    </tbody>
                                </v-table>
                            </div>
                        </div>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col>
                        <PaginationComponent v-if="loading" :current-page="currentPage" :items-per-page="itemsPerPage"
                            :total-items="totalItems" :total-pages="totalPages"
                            @current-page-updated="currentPageUpdated" />
                    </v-col>
                </v-row>
            </v-responsive>
        </v-layout>
    </div>
</template>