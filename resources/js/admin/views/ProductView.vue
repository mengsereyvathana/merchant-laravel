<script setup lang="ts">
import Swal from 'sweetalert2';
import PaginationComponent from '../components/PaginationComponent.vue';
import SearchComponent from '../components/SearchComponent.vue';
import _ from 'lodash';
import { Upload } from '../service/helpers';
import { onMounted, ref, defineEmits } from 'vue';
import { RouterLink } from 'vue-router';
import { productService } from '../service/api/modules/product.api'
import { IProductItem } from '../types/Product';

const emits = defineEmits(['update-total-pages']);
const header: string[] = ['PRODUCT NAME', 'DESCRIPTION', 'PRICE', 'BUY', 'MARGIN', 'STOCK', 'RAM', 'STORAGE', 'COLOR', 'CATEGORY', 'PUBLISHED ON'];

let products = ref<IProductItem[]>([]);

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
    { id: 1, title: "Name", by: "name" },
    { id: 2, title: "ID", by: "id" },
]);

const currentSearchUpdated = async (value: string, selectOptions: string) => {
    selectedSearchOption.value = selectOptions;
    searchQuery.value = value;
    await search();
}

const currentPageUpdated = (value: number): void => {
    if (searchQuery.value !== "") {
        search(value);
    } else if (currentPage.value !== value) {
        getProducts(value);
    }
};

onMounted(async () => {
    selectedSearchOption.value = searchOption.value[0].by;
    await getProducts();
});

const setPagination = (pn: number, ipp: number, ti: number, tp: number) => {
    currentPage.value = pn;
    itemsPerPage.value = ipp;
    totalItems.value = ti;
    totalPages.value = tp;
}

const getProducts = _.debounce(async (pageNumber = 1) => {
    const [error, data] = await productService.getAllProducts(pageNumber)
    if (error) console.log(error);
    else {
        if (data.success) {
            const productItem = data.data as IProductItem[]
            setPagination(pageNumber, data.per_page, data.total_item, data.total_page)
            products.value = productItem;
            paginationLoaded.value = true;
        }
    }
}, 200)

const search = _.debounce(async (pageNumber = 1) => {
    const params = {
        [selectedSearchOption.value]: searchQuery.value,
        page: pageNumber,
    }
    const [error, data] = await productService.searchProducts(params)
    if (error) console.log(error);
    else {
        if (data.success) {
            products.value = data.data as IProductItem[]
            setPagination(pageNumber, data.per_page, data.total_item, data.total_page)
        }
        else {
            getProducts(currentPage.value);
        }
    }
}, 200)

const deleteProduct = async (id: number) => {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: "You want to delete this product!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        confirmButtonColor: '#42b883',
        cancelButtonColor: '#d33',
        reverseButtons: true
    })
    if (result.isConfirmed) {
        const [error, data] = await productService.deleteProduct(id)
        if (error) console.log(error)
        else {
            if (data.success) {
                if ((products.value.length - 1) % itemsPerPage.value == 0) {
                    currentPage.value = currentPage.value - 1;
                    totalPages.value = totalPages.value - 1;
                }
                await getProducts(currentPage.value);
                Swal.fire({
                    toast: true,
                    position: 'top',
                    showClass: {
                        icon: 'animated heartBeat delay-1s'
                    },
                    icon: 'success',
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1000
                });
            }
        }
    }
}

const updateEnable = async (id: number, enable: string) => {
    const formData = new FormData();
    formData.append('action', enable === '1' ? '0' : '1');
    formData.append('_method', 'PUT');

    const [error, data] = await productService.editProduct(id, formData)
    if (error) console.log(error)
    else {
        if (data.success) {
            await getProducts(currentPage.value);
            Swal.fire({
                toast: true,
                position: 'top',
                showClass: {
                    icon: 'animated heartBeat delay-1s'
                },
                icon: 'success',
                text: data.message,
                showConfirmButton: false,
                timer: 1000
            })
        }
    }
}

</script>

<template>
    <div>
        <div class='d-flex flex-row justify-space-between align-center flex-wrap mb-4'>
            <h1 class='text-header text-grey-darken-2 font-weight-medium'>Product</h1>
            <RouterLink to="/admin/add_product">
                <v-btn prepend-icon="mdi-plus" color="success" flat>
                    Add Product
                </v-btn>
            </RouterLink>
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
                        <v-table class='w-[1100px] md:w-full'>
                            <thead>
                                <tr>
                                    <th class='px-3'>
                                        <v-checkbox hide-details></v-checkbox>
                                    </th>
                                    <th v-for="(item, index) in header" :key="index"
                                        :class="index == 5 ? 'w-[200px] text-start' : index == 0 ? 'w-[300px] text-center' : 'text-start'"
                                        class="text-grey-darken-2 py-2 px-3">{{ item }}</th>
                                    <th class='text-grey-darken-2 py-2 px-3 pr-4'>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr class='group relative ' v-for="item in products" :key="item.id">
                                    <td class="px-3">
                                        <v-checkbox hide-details class="w-[40px]"></v-checkbox>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-row align-center">
                                            <v-img :src="Upload.image(item.image)" alt="" aspect-ratio="1/1"
                                                class='rounded-md mr-3' cover :max-width="50" :width="50"></v-img>
                                            <RouterLink :to="'/admin/edit_product/' + item.id">
                                                <v-hover>
                                                    <template v-slot:default="{ isHovering, props }">
                                                        <span v-bind="props" class="text-grey-darken-3"
                                                            :class="isHovering ? 'text-grey-darken-4' : ''">{{ item.name
                                                            }}</span>
                                                    </template>
                                                </v-hover>

                                            </RouterLink>
                                        </div>
                                    </td>
                                    <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{ item.description }}</td>
                                    <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{ item.price }}</td>
                                    <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{ item.buy }}</td>
                                    <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{ item.margin }}</td>
                                    <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{ item.stock }}</td>
                                    <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{ item.ram }}</td>
                                    <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{ item.storage }}</td>
                                    <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{ item.color }}</td>
                                    <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{ item.category?.name }}</td>
                                    <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{ item.created_at }}</td>
                                    <td class='py-2 px-3 pr-5'>
                                        <v-btn icon="mdi-dots-horizontal" variant="text"></v-btn>
                                    </td>

                                    <div
                                        class='hidden group-hover:flex absolute right-0 top-[50%] translate-y-[-50%] pr-[10px]  gap-1'>
                                        <RouterLink :to="'/admin/add_product_scheme/' + item.id">
                                            <v-btn size="small" icon="mdi-plus" color="blue" flat></v-btn>
                                        </RouterLink>
                                        <v-btn @click="updateEnable(item.id, item.action)" size="small"
                                            :icon="item.action === '1' ? 'mdi-eye' : 'mdi-eye-off'" color="green" flat>
                                        </v-btn>
                                        <RouterLink :to="'/admin/edit_product/' + item.id">
                                            <v-btn size="small" icon="mdi-pencil" color="blue" flat></v-btn>
                                        </RouterLink>
                                        <v-btn @click="deleteProduct(item.id)" size="small" icon="mdi-delete" color="red"
                                            flat> </v-btn>
                                    </div>
                                </tr>
                            </tbody>
                        </v-table>
                    </v-col>
                </v-row>
                <v-row>
                    <v-col>
                        <PaginationComponent v-if="paginationLoaded" :current-page="currentPage"
                            :items-per-page="itemsPerPage" :total-items="totalItems" :total-pages="totalPages"
                            @current-page-updated="currentPageUpdated" />
                    </v-col>
                </v-row>
            </v-responsive>
        </v-layout>
    </div>
</template>
