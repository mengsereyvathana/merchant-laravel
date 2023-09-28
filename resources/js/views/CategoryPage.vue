<script setup lang="ts">
import { Upload, Crypt } from '@/services/helper/index';
import { onMounted, ref } from 'vue';
import { RouterLink } from 'vue-router';
import { ICategoryItem } from "../types/Category";
import { categoryService } from '@/services/api/modules/category.api'

const categories = ref<ICategoryItem[]>([]);

onMounted(() => {
    getCategory();
})
const getCategory = async () => {
    try {
        const [error, data] = await categoryService.getAllCategories();
        if (error) console.log(error);
        else {
            if (data.success) {
                categories.value = data.data;
            }
        }
    } catch (error) {
        console.log(error)
    }
}

const idDetail = (id: number): string | null => {
    return Crypt.encrypt(id.toString())
}

</script>
<template>
    <div class="mt-1 p-4 bg-white">
        <div class="flex justify-between items-end">
            <div class="flex items-center">
                <img :src="Upload.icon('category.svg')" alt="" class='w-[20px] h-[20px] mr-1' />
                <span class='min-[320px]:text-sm text-2xl font-[700] text-gray-800 capitalize'>Categories</span>
            </div>
        </div>
        <div class="grid min-[320px]:grid-cols-2 min-[320px]:gap-3 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-9 mt-5">
            <!-- loop -->
            <div v-for="item in categories" :key="item.id">
                <RouterLink :to="'/product_category/' + idDetail(item.id)">
                    <div
                        class="bg-white cursor-pointer relative rounded-[5px] shadow-[0_1px_3px_rgb(3,0,71,0.09)] overflow-hidden group">
                        <div class="w-full h-[150px]">
                            <img :src="item.image" alt='' class='w-full h-[150px] object-cover' />
                        </div>
                        <div class="p-2">
                            <h3 class='text-md text-gray-500 cursor-pointer truncate'>{{ }}</h3>
                            <div class="flex justify-center items-center">
                                <div class="flex items-center gap-2 text-sm">
                                    <h4 class='text-main text-base font-semibold'> {{ item.name }} </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </RouterLink>
            </div>
        </div>
    </div>
</template>