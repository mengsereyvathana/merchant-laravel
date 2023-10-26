<script setup lang="ts">
import Swal from 'sweetalert2';
import PaginationComponent from '../components/PaginationComponent.vue';
import SearchComponent from '../components/SearchComponent.vue'
import PopupComponent from '../components/PopupComponent.vue';
import _ from "lodash";
import { Upload } from '../service/helpers';
import { computed } from '@vue/reactivity';
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import { productSchemeService } from '../service/api/modules/product-scheme.api'
import { IProductSchemeItem } from '../types/ProductScheme';

const header: string[] = ['PRODUCT SCHEME NAME', "SCHEME", "UNIT PRICE", "MARGIN", 'PUBLISHED ON'];

let productScheme = ref<IProductSchemeItem[]>([]);
let loadingUpdate = ref<boolean>(false);
let loadingDelete = ref<boolean>(false);

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

onMounted(async () => {
    selectedSearchOption.value = searchOption.value[0].by;
    await getProductScheme();
});

const isConfirmedUpdated = (value: boolean, itemId: number) => {
    if (value) {
        deleteProductScheme(itemId);
    }
}

const currentSearchUpdated = async (value: string, selectOptions: string) => {
    selectedSearchOption.value = selectOptions;
    searchQuery.value = value;
    await search();
}

const currentPageUpdated = (value: number): void => {
    if (searchQuery.value != "") {
        search(value);
    } else if (currentPage.value !== value) {
        getProductScheme(value);
    }
};

const setPagination = (pn: number, ipp: number, ti: number, tp: number) => {
    currentPage.value = pn;
    itemsPerPage.value = ipp;
    totalItems.value = ti;
    totalPages.value = tp;
}

const getProductScheme = async (pageNumber = 1) => {
    const [error, data] = await productSchemeService.getAllProductScheme(pageNumber)
    if (error) console.log(error);
    else {
        if (data.success) {
            productScheme.value = data.data as IProductSchemeItem[]
            currentPage.value = pageNumber;
            itemsPerPage.value = data.per_page;
            totalItems.value = data.total_item;
            totalPages.value = data.total_page;
            paginationLoaded.value = true;
        }
    }
}

const search = _.debounce(async (pageNumber = 1) => {
    const params = {
        [selectedSearchOption.value]: searchQuery.value,
        page: pageNumber,
    }
    const [error, data] = await productSchemeService.searchProducts(params)
    if (error) console.log(error);
    else {
        if (data.success) {
            productScheme.value = data.data as IProductSchemeItem[]
            setPagination(pageNumber, data.per_page, data.total_item, data.total_page)
        } else {
            getProductScheme(currentPage.value)
        }
    }
}, 400)

const deleteProductScheme = async (id: number) => {
    // const result = await Swal.fire({
    //     title: 'Are you sure?',
    //     text: "You want to delete this Product!",
    //     icon: 'warning',
    //     showCancelButton: true,
    //     confirmButtonText: 'Yes, delete it!',
    //     cancelButtonText: 'No, cancel!',
    //     confirmButtonColor: '#42b883',
    //     cancelButtonColor: '#d33',
    //     reverseButtons: true
    // })
    // if (result.isConfirmed) {

    // }
    loadingDelete.value = true;
    // Swal.fire({
    //     position: 'center',
    //     allowEscapeKey: false,
    //     allowOutsideClick: false,
    //     showConfirmButton: false,
    //     didOpen: () => {
    //         Swal.showLoading();
    //     }
    // })
    const [error, data] = await productSchemeService.deleteProductScheme(id)
    if (error) console.log(error)
    else {
        if (data.success) {
            Swal.close();
            if ((productScheme.value.length - 1) % itemsPerPage.value == 0) {
                currentPage.value = currentPage.value - 1;
            }
            await getProductScheme(currentPage.value);
            // Swal.fire({
            //     toast: true,
            //     position: 'top',
            //     showClass: {
            //         icon: 'animated heartBeat delay-1s'
            //     },
            //     icon: 'success',
            //     text: 'Product has been delete!',
            //     showConfirmButton: false,
            //     timer: 1000
            // });
            loadingDelete.value = false;
        }
    }
}

const updateEnable = async (id: number, enable: string) => {
    loadingUpdate.value = true;

    const formData = new FormData();
    formData.append('id', id.toString())
    formData.append('action', enable === '1' ? '0' : '1');
    formData.append('_method', 'PUT');

    const [error, data] = await productSchemeService.editProductScheme(id, formData)
    if (error) console.log(error)
    else {
        if (data.success) {
            await getProductScheme(currentPage.value)
            loadingUpdate.value = false;
        }
    }
}
</script>

<template>
    <div>
        <div class='d-flex flex-row justify-space-between align-center flex-wrap mb-4'>
            <h1 class='text-header text-grey-darken-2 font-weight-medium'>Product Scheme</h1>
            <RouterLink to="/admin/show_product">
                <v-btn prepend-icon="mdi-plus" color="success" flat>
                    Add Product Scheme
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
                                <tr class='group relative ' v-for="item in productScheme" :key="item.id">
                                    <td class="px-3">
                                        <v-checkbox hide-details class="w-[40px]"></v-checkbox>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-row align-center">
                                            <v-img :src="Upload.image(item.products?.image)" alt="" aspect-ratio="1/1"
                                                class='rounded-md mr-3' cover :max-width="50" :width="50"
                                                :height="50"></v-img>
                                            <RouterLink :to="'/admin/edit_product_scheme/' + item.id">
                                                <v-hover>
                                                    <template v-slot:default="{ isHovering, props }">
                                                        <span v-bind="props" class="text-grey-darken-3"
                                                            :class="isHovering ? 'text-grey-darken-4' : ''">{{
                                                                item.products?.name
                                                            }}</span>
                                                    </template>
                                                </v-hover>
                                            </RouterLink>
                                        </div>
                                    </td>
                                    <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{ item.scheme_id }}</td>
                                    <td class='py-2 px-3 text-body-2 text-grey-darken-3'>${{ item.unit_price }}</td>
                                    <td class='py-2 px-3 text-body-2 text-grey-darken-3'>${{ item.margin }}</td>
                                    <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{ item.created_at }}</td>
                                    <td class='py-2 px-3 pr-5'><v-btn icon="mdi-dots-horizontal" variant="text"></v-btn>
                                    </td>
                                    <div
                                        class='hidden group-hover:flex absolute right-0 top-[50%] translate-y-[-50%] pr-[10px]  gap-1'>
                                        <v-btn @click="updateEnable(item.id, item.action)" size="small"
                                            :icon="item.action === '1' ? 'mdi-eye' : 'mdi-eye-off'" :loading="loadingUpdate"
                                            color="green" flat>
                                        </v-btn>
                                        <RouterLink :to="'/admin/edit_product_scheme/' + item.id">
                                            <v-btn size="small" icon="mdi-pencil" color="blue" flat></v-btn>
                                        </RouterLink>
                                        <PopupComponent :id="item.id" icon="mdi-delete" title="Delete This Item"
                                            text="Are you sure?" :loading="loadingDelete"
                                            @is-confirmed="isConfirmedUpdated" />
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
