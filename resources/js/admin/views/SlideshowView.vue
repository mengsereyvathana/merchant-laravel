<script setup lang="ts">
import Swal from 'sweetalert2';
import PaginationComponent from '../components/PaginationComponent.vue';
import SearchComponent from '../components/SearchComponent.vue'
import _ from "lodash"
import { Upload } from '../service/helpers';
import { computed } from '@vue/reactivity';
import { onMounted, ref } from 'vue';
import { RouteParams, RouterLink, useRoute } from 'vue-router';
import { slideshowService } from '../service/api/modules/slideshow.api'
import { ISlideshowItem } from '../types/Slideshow';

const header: string[] = ['SLIDESHOW NAME', "TAG", "LINK", "ORDER", 'PUBLISHED ON'];

let slideshows = ref<ISlideshowItem[]>([]);

//paginate
let currentPage = ref<number>(1);
let itemsPerPage = ref<number>(0);
let totalItems = ref<number>(0);
let totalPages = ref<number>(0);
let isLoaded = ref<boolean>(false);

//search
let isFound = ref(false);
let selectedSearchOption = ref("");
let searchQuery = ref("");
let searchOption = ref([
    { id: 1, title: "Title", by: "title" },
    { id: 2, title: "Order", by: "slide_order" },
    { id: 3, title: "Tag", by: "tage" },
]);

onMounted(async () => {
    selectedSearchOption.value = searchOption.value[0].by;
    await getSlideshows();
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

const currentPageUpdated = (value: number) => {
    if (searchQuery.value !== "") {
        search(value);
    } else if (currentPage.value !== value) {
        getSlideshows(value);
    }
};

const getSlideshows = async (pageNumber = 1) => {
    const [error, data] = await slideshowService.getAllSlideshows(pageNumber)
    if (error) console.log(error);
    else {
        if (data.success) {
            isFound.value = true;
            slideshows.value = data.data as ISlideshowItem[]
            setPagination(pageNumber, data.per_page, data.total_item, data.total_page)
            isLoaded.value = true;
        }
    }
}

const search = _.debounce(async (pageNumber = 1) => {
    const params = {
        [selectedSearchOption.value]: searchQuery.value,
        page: pageNumber,
    }
    const [error, data] = await slideshowService.searchSlideshows(params)
    if (error) console.log(error);
    else {
        if (data.success) {
            slideshows.value = data.data as ISlideshowItem[]
            setPagination(pageNumber, data.per_page, data.total_item, data.total_page)
            isFound.value = data.total_item !== 0;
        } else {
            getSlideshows()
        }
    }
}, 500)

const deleteProduct = async (id: number) => {
    const result = await Swal.fire({
        title: 'Are you sure?',
        text: "You want to delete this Slideshow!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'No, cancel!',
        confirmButtonColor: '#42b883',
        cancelButtonColor: '#d33',
        reverseButtons: true
    })
    if (result.isConfirmed) {
        Swal.fire({
            position: 'center',
            allowEscapeKey: false,
            allowOutsideClick: false,
            showConfirmButton: false,
            didOpen: () => {
                Swal.showLoading();
            }
        })
        const [error, data] = await slideshowService.deleteSlideshow(id)
        if (error) console.log(error)
        else {
            if (data.success) {
                await getSlideshows();
                Swal.close();
                if ((slideshows.value.length - 1) % itemsPerPage.value == 0) {
                    currentPage.value = currentPage.value - 1;
                }
                Swal.fire({
                    toast: true,
                    position: 'top',
                    showClass: {
                        icon: 'animated heartBeat delay-1s'
                    },
                    icon: 'success',
                    text: 'Product has been delete!',
                    showConfirmButton: false,
                    timer: 1000
                });
            }
        }
    }
}

const updateEnable = async (id: number, action: string) => {
    const formData = new FormData();
    formData.append('action', action === '1' ? '0' : '1');
    formData.append('_method', 'PUT');

    const [error, data] = await slideshowService.editSlideshow(id, formData)
    if (error) console.log(error)
    else {
        if (data.success) {
            await getSlideshows(currentPage.value)
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
    <div class="lg:py-3 lg:px-8 p-3">
        <div class='flex justify-between items-center mb-4'>
            <h1 class='text-header font-bold text-gray-600'>Slideshow</h1>
            <RouterLink to="/admin/add_slideshow">
                <button
                    class='flex items-center gap-1 px-4 py-3 rounded-md bg-main text-white text-sm cursor-pointer font-medium'>
                    <img :src="Upload.icon('plus.svg')" alt="" />Add Slideshow
                </button>
            </RouterLink>
        </div>
        <div
            class='relative overflow-hidden border-solid border-b border-gray-200 pt-3 shadow-[#E1E1E1_0px_1px_8px] rounded-md'>
            <div class="flex gap-4 flex-col md:flex-row lg:items-center justify-between px-3 mb-4">
                <SearchComponent :search-option="searchOption" @current-search-updated="currentSearchUpdated" />
            </div>
            <div
                class="w-full px-2 overflow-x-auto overflow-y-none scroll-smooth scrollbar-thin scrollbar-track-gray-200 scrollbar-track-rounded-xl scrollbar-thumb-current scrollbar-thumb-rounded-xl ">
                <table v-if="isLoaded && isFound" class='w-[1100px] md:w-full'>
                    <thead class='border-solid border-b border-gray-300'>
                        <tr>
                            <th class='text-start text-gray-600 text-md w-[50px] py-2 px-3'>
                                <input type="checkbox" name="" id=""
                                    class='w-4 h-4 border-solid border border-gray-500 rounded-[4px] checked:rounded-[4px]' />
                            </th>
                            <th v-for="(item, index) in header" :key="index"
                                :class="index == 5 ? 'w-[200px] text-start' : index == 0 ? 'w-[300px] text-center' : 'text-start'"
                                class="text-gray-600 text-sm py-2 px-3">{{ item }}</th>
                            <th class='text-end text-gray-600 text-sm py-2 px-3 pr-4'></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class='group relative border-solid border-b border-gray-300' v-for="item in slideshows"
                            :key="item.id">
                            <td class='text-sm text-gray-700 py-2 px-3'>
                                <input type="checkbox" name="" id=""
                                    class='w-4 h-4 cursor-pointer border-solid border border-gray-500 rounded-[4px] checked:rounded-[4px]' />
                            </td>
                            <td class='py-2 px-3 flex items-center gap-1'>
                                <img :src="Upload.image(item.image)" alt=""
                                    class='w-[45px] h-[45px] border-solid border border-gray-300 rounded-md object-cover' />
                                <RouterLink :to="'/admin/edit_slideshow/' + item.id">
                                    <p
                                        class='w-[230px] text-ph font-semibold truncate text-main py-2 px-3 cursor-pointer hover:underline'>
                                        {{ item.title }}
                                    </p>
                                </RouterLink>
                            </td>
                            <td class='text-ph font-semibold text-gray-700 py-2 px-3'>{{ item.tage }}</td>
                            <td class='text-ph font-semibold text-gray-700 py-2 px-3'>{{ item.link }}</td>
                            <td class='text-ph font-semibold text-gray-700 py-2 px-3'>{{ item.slide_order }}</td>
                            <td class='text-ph font-semibold text-gray-700 py-2 px-3'>{{ item.created_at }}</td>
                            <td class='text-lg w-4 text-gray-700 py-2 px-3 pr-5'>
                                <div class="w-4 h-4"><img :src="Upload.icon('more.svg')" alt="" /></div>
                            </td>

                            <div
                                class='hidden group-hover:flex absolute right-0 top-[50%] translate-y-[-50%] pr-[10px]  gap-1'>
                                <!-- <button @click="moveUp()"
                                    class='inline-flex px-[10px] py-[6px] bg-body border-solid border border-gray-300 rounded-md cursor-pointer hover:bg-gray-200'>
                                    <img :src="Upload.icon('up.svg')" alt="" class="w-[14px] h-[14px]">
                                </button>
                                <button @click="moveDown()"
                                    class='inline-flex px-[10px] py-[6px] bg-body border-solid border border-gray-300 rounded-md cursor-pointer hover:bg-gray-200'>
                                    <img :src="Upload.icon('down.svg')" alt="" class="w-[14px] h-[14px]">
                                </button> -->
                                <div @click="updateEnable(item.id, item.action)"
                                    class='inline-flex px-[10px] py-[6px] bg-body border-solid border border-gray-300 rounded-md cursor-pointer hover:bg-gray-200'>
                                    <img :src="Upload.icon(item.action === '1' ? 'eyeon.svg' : 'eyeoff.svg')" alt=""
                                        class="w-[14px] h-[14px]">
                                </div>
                                <RouterLink :to="'/admin/edit_slideshow/' + item.id">
                                    <div
                                        class='inline-flex px-[10px] py-[6px] bg-body border-solid border border-gray-300 rounded-md cursor-pointer hover:bg-gray-200'>
                                        <img :src="Upload.icon('edit.svg')" alt="" class="w-[14px] h-[14px]">
                                    </div>
                                </RouterLink>
                                <div @click="deleteProduct(item.id)"
                                    class='inline-flex px-[10px] py-[6px] bg-body border-solid border border-gray-300 rounded-md cursor-pointer hover:bg-gray-200'>
                                    <img :src="Upload.icon('trash.svg')" alt="" class="w-[14px] h-[14px]">
                                </div>
                            </div>
                        </tr>
                    </tbody>
                </table>
                <div v-else-if="isLoaded && isFound === false">
                    <h1 class="font-semibold text-center">No Product founded</h1>
                </div>
            </div>

            <div class="flex items-center justify-between border-t border-gray-200 bg-white px-2 py-3 sm:px-2">
                <PaginationComponent v-if="isLoaded" :current-page="currentPage" :items-per-page="itemsPerPage"
                    :total-items="totalItems" :total-pages="totalPages" @current-page-updated="currentPageUpdated" />
            </div>
        </div>
    </div>
</template>
