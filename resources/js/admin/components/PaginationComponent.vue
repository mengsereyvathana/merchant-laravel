<script setup lang="ts">
import { computed, defineProps, defineEmits, onMounted } from 'vue';
import { Upload } from '@/admin/service/helpers';

const emits = defineEmits(['currentPageUpdated']);
const currentPage = computed<number>(() => props.currentPage);
const itemsPerPage = computed<number>(() => props.itemsPerPage);
const totalItems = computed<number>(() => props.totalItems);
const totalPages = computed<number>(() => props.totalPages);

const props = defineProps<{
    currentPage: number,
    itemsPerPage: number,
    totalItems: number,
    totalPages: number;
}>();

const emitPage = (page: number): void => {
    emits('currentPageUpdated', page);
    // currentPage.value = page;
};

const pageItems = computed<number[]>(() => {
    const pages = [];
    const range = 2;
    if (totalPages.value < range + 3) {
        for (let i = 1; i <= totalPages.value; i++) {
            pages.push(i);
        }
    } else {
        const startPage = Math.max(currentPage.value - range, 1);
        const endPage = Math.min(currentPage.value + range, totalPages.value);

        for (let i = startPage; i <= endPage; i++) {
            pages.push(i);
        }

        if (startPage > 1) {
            pages.unshift(-1);
            pages.unshift(1);
        }

        if (endPage < totalPages.value) {
            pages.push(-1);
            pages.push(totalPages.value);
        }

    }
    return pages;
});

</script>

<template>
    <div class="flex flex-1 justify-between sm:hidden">
        <button v-if="currentPage > 1" @click="emitPage(currentPage > 1 ? currentPage - 1 : currentPage)"
            class="relative inline-flex items-center rounded-md border border-gray-300 bg-gray-50 border-solid px-4 py-2 text-sm font-medium text-gray-700 hover:bg-body cursor-pointer">
            Previous
        </button>
        <button v-if="currentPage < totalPages" @click="emitPage(currentPage < totalPages ? currentPage + 1 : currentPage)"
            class="relative ml-3 inline-flex items-center rounded-md border border-gray-300 bg-gray-50 border-solid px-4 py-2 text-sm font-medium text-gray-700 hover:bg-body cursor-pointer">
            Next
        </button>
    </div>

    <div class="hidden sm:flex sm:flex-1 sm:items-center sm:justify-between">
        <div>
            <p class="text-sm text-gray-500 font-semibold">Showing
                <span class="font-semibold">{{ totalItems > 0 ? (currentPage - 1) * itemsPerPage + 1 :
                    0 }}</span> to
                <span class="font-semibold">{{ (totalItems - (currentPage - 1) * itemsPerPage) >
                    itemsPerPage
                    ? currentPage * itemsPerPage : totalItems }}</span> of
                <span class="font-semibold">{{ totalItems }}</span> entries
            </p>
        </div>
        <div>
            <nav class="flex gap-2 rounded-md" aria-label="Pagination">
                <button v-if="currentPage > 1" @click="emitPage(currentPage > 1 ? currentPage - 1 : currentPage)"
                    class="relative cursor-pointer inline-flex items-center rounded-l-md px-2 py-1 text-sm font-medium text-gray-500 bg-gray-50 hover:bg-gray-200 focus:z-20 border-solid border">
                    <img :src="Upload.icon('left.svg')" alt="" class="h-4 w-4">
                    <span class="text-xs mr-1">Previous</span>
                </button>

                <div v-for="page in pageItems" :key="page">
                    <button v-if="page != -1" @click="emitPage(page)"
                        class="relative rounded-md cursor-pointer inline-flex items-center border-solid border px-3 py-1 text-sm font-medium focus:z-20"
                        :class="page == currentPage ? 'bg-primary border-primary text-white hover:bg-primary' : 'border-gray-300 text-gray-500 bg-gray-50 hover:bg-gray-200'">
                        {{ page }}
                    </button>
                    <span v-else class="mx-2">...</span>
                </div>

                <button v-if="currentPage < totalPages"
                    @click="emitPage(currentPage < totalPages ? currentPage + 1 : currentPage)"
                    class="relative cursor-pointer inline-flex items-center rounded-r-md px-2 py-1 text-sm font-medium text-gray-500 bg-gray-50 hover:bg-gray-200 focus:z-20 border-solid border">
                    <span class="text-xs mr-1">Next</span>
                    <img :src="Upload.icon('right.svg')" alt="" class="h-4 w-4">
                </button>
            </nav>
        </div>
    </div>
</template>
  
