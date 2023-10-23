<script setup lang="ts">
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import PaginationComponent from '../components/PaginationComponent.vue';
import SearchComponent from '../components/SearchComponent.vue';
import _ from 'lodash';
import { Upload } from '../service/helpers';
import { categoryService } from '../service/api/modules/category.api'
import { ICategoryItem } from '../types/Category';

const header: string[] = ['CATEGORY NAME', 'DESCRIPTION', 'PUBLISHED ON'];

let categories = ref<ICategoryItem[]>([]);
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
]);

onMounted(async () => {
    selectedSearchOption.value = searchOption.value[0].by;
    getCategories();
});

const setPagination = (pn: number, ipp: number, ti: number, tp: number) => {
    currentPage.value = pn;
    itemsPerPage.value = ipp;
    totalItems.value = ti;
    totalPages.value = tp;
}

const currentSearchUpdated = async (value: string, selectOptions: string) => {
    selectedSearchOption.value = selectOptions;
    searchQuery.value = value;
    await search();
}

const currentPageUpdated = (value: number): void => {
    if (searchQuery.value !== "") {
        search(value);
    } else if (currentPage.value !== value) {
        getCategories(value);
    }
};

const getCategories = async (pageNumber = 1) => {
    const [error, data] = await categoryService.getAllCategories(pageNumber)
    if (error) console.log(error);
    else {
        const item = data.data as ICategoryItem[];
        categories.value = item;
        setPagination(pageNumber, data.per_page, data.total_item, data.total_page)
        paginationLoaded.value = true;
    }
}


const search = _.debounce(async (pageNumber = 1) => {
    const params = {
        [selectedSearchOption.value]: searchQuery.value,
        page: pageNumber,
    }
    const [error, data] = await categoryService.searchCategories(params)
    if (error) console.log(error);
    else {
        if (data.success) {
            categories.value = data.data as ICategoryItem[]
            setPagination(pageNumber, data.per_page, data.total_item, data.total_page)
        }
        else {
            getCategories();
        }
    }
}, 500)

const deleteCategory = (id: number) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You want to delete this category!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        confirmButtonColor: '#42b883',
        cancelButtonColor: '#d33',
        reverseButtons: true
    }).then(async (result) => {
        if (result.isConfirmed) {
            loadingDelete.value = true;
            const [error, data] = await categoryService.deleteCategory(id)
            if (error) console.log(error)
            else {
                if (data.success) {
                    if ((categories.value.length - 1) % itemsPerPage.value == 0) {
                        currentPage.value = currentPage.value - 1;
                        // totalPages.value = totalPages.value - 1;
                    }
                    await getCategories(currentPage.value);
                    loadingDelete.value = false;
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
                }
            }
        }
    })
}

const updateEnable = async (id: number, action: string) => {
    loadingUpdate.value = true;

    const formData = new FormData();
    formData.append('action', action === '1' ? '0' : '1');
    formData.append('_method', 'PUT');

    const [error, data] = await categoryService.editCategory(id, formData)
    if (error) console.log(error)
    else {
        if (data.success) {
            await getCategories(currentPage.value)
            // Swal.fire({
            //     toast: true,
            //     position: 'top',
            //     showClass: {
            //         icon: 'animated heartBeat delay-1s'
            //     },
            //     icon: 'success',
            //     text: data.message,
            //     showConfirmButton: false,
            //     timer: 1000
            // })
            loadingUpdate.value = false;
        }
    }
}

</script>

<template>
    <div>
        <!-- <div class='flex justify-between items-center mb-4'>
            <h1 class='text-xl font-bold text-gray-600'>Category</h1>
            <RouterLink to="/admin/add_slideshow">
                <button
                    class='flex items-center gap-1 px-4 py-3 rounded-md bg-main text-white text-sm cursor-pointer font-medium'>
                    <img :src="Upload.icon('plus.svg')" alt="" />Add Slideshow
                </button>
            </RouterLink>
        </div> -->
        <div class='d-flex flex-row justify-space-between align-center flex-wrap mb-4'>
            <h1 class='text-header text-grey-darken-2 font-weight-medium'>Category</h1>
            <RouterLink to="/admin/add_category">
                <v-btn prepend-icon="mdi-plus" color="success" flat>
                    Add Category
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
                                <tr class='group relative ' v-for="item in categories" :key="item.id">
                                    <td class="px-3">
                                        <v-checkbox hide-details class="w-[40px]"></v-checkbox>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-row align-center">
                                            <v-img :src="Upload.image(item.image)" alt="" aspect-ratio="1/1"
                                                class='rounded-md mr-3' cover :max-width="50" :width="50"></v-img>
                                            <RouterLink :to="'/admin/edit_category/' + item.id">
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
                                    <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{ item.created_at }}</td>
                                    <td class='py-2 px-3 pr-5'>
                                        <v-btn icon="mdi-dots-horizontal" variant="text"></v-btn>
                                    </td>

                                    <div
                                        class='hidden group-hover:flex absolute right-0 top-[50%] translate-y-[-50%] pr-[10px]  gap-1'>
                                        <v-btn @click="updateEnable(item.id, item.action)" size="small"
                                            :icon="item.action === '1' ? 'mdi-eye' : 'mdi-eye-off'" color="green"
                                            :loading="loadingUpdate" flat>
                                        </v-btn>
                                        <RouterLink :to="'/admin/edit_category/' + item.id">
                                            <v-btn size="small" icon="mdi-pencil" color="blue" flat></v-btn>
                                        </RouterLink>
                                        <v-btn @click="deleteCategory(item.id)" size="small" icon="mdi-delete" color="red"
                                            :loading="loadingDelete" flat> </v-btn>
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
