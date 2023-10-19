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
    <div class="d-flex justify-space-between d-sm-none">
        <v-btn v-if="currentPage > 1" @click="emitPage(currentPage > 1 ? currentPage - 1 : currentPage)" variant="tonal"
            prepend-icon="mdi-chevron-left" class="text-none text-grey-darken-1">
            Previous
        </v-btn>
        <v-btn v-if="currentPage < totalPages" @click="emitPage(currentPage < totalPages ? currentPage + 1 : currentPage)"
            variant="tonal" append-icon="mdi-chevron-right" class="text-none text-grey-darken-1">
            Next
        </v-btn>
    </div>

    <div class="d-none d-sm-flex align-sm-center justify-sm-space-between w-full">
        <div>
            <p class="text-none text-grey-darken-1 font-weight-medium">Showing
                <span>{{ totalItems > 0 ? (currentPage - 1) * itemsPerPage + 1 :
                    0 }}</span> to
                <span>{{ (totalItems - (currentPage - 1) * itemsPerPage) >
                    itemsPerPage
                    ? currentPage * itemsPerPage : totalItems }}</span> of
                <span>{{ totalItems }}</span> entries
            </p>
        </div>
        <div>
            <nav class="flex gap-2 rounded-md" aria-label="Pagination">
                <v-btn v-if="currentPage > 1" prepend-icon="mdi-chevron-left" variant="tonal"
                    class="text-none text-grey-darken-1" @click="emitPage(currentPage > 1 ? currentPage - 1 : currentPage)">
                    <!-- <img :src="Upload.icon('left.svg')" alt="" class="h-4 w-4"> -->
                    Previous
                </v-btn>

                <div v-for="page in pageItems" :key="page">
                    <v-btn v-if="page !== -1" @click="emitPage(page)" variant="tonal" class="text-none text-grey-darken-1"
                        :class="page == currentPage ? 'bg-success' : ''" max-width="50">
                        {{ page }}
                    </v-btn>
                    <span v-else class="mx-2">...</span>
                </div>

                <v-btn v-if="currentPage < totalPages" append-icon="mdi-chevron-right" variant="tonal"
                    class="text-none text-grey-darken-1"
                    @click="emitPage(currentPage < totalPages ? currentPage + 1 : currentPage)">
                    Next
                    <!-- <img :src="Upload.icon('right.svg')" alt="" class="h-4 w-4"> -->
                </v-btn>
            </nav>
        </div>
    </div>
</template>
  
