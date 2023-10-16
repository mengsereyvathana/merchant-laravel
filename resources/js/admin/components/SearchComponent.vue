<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { defineEmits } from 'vue';
import { defineProps } from 'vue';

const emits = defineEmits(['currentSearchUpdated']);

interface ISearchOption {
    id: number;
    title: string;
    by: string;
}

const props = defineProps<{
    searchOption: ISearchOption[]
}>();

let selectedSearchOption = ref("");
let searchQuery = ref("");
let searchOption = ref(props.searchOption);

onMounted(async () => {
    selectedSearchOption.value = searchOption.value[0].by;
});

const search = () => {
    emits('currentSearchUpdated', searchQuery.value, selectedSearchOption.value);
};

</script>

<template>
    <div class="flex gap-3">
        <div class='relative'>
            <input @keyup="search()" v-model="searchQuery" type="text" name="" id="" placeholder='Search'
                class='text-sm pl-3 w-full lg:w-[180px] h-[40px] rounded-md border-gray-300 border-solid border focus:border-current focus:ring-current' />
            <!-- <img :src="Upload.icon('search.svg')" alt=""
                            class="absolute top-[50%] left-3 translate-y-[-50%] w-4 h-4" /> -->
        </div>
        <div class="flex flex-row justify-between items-center gap-3">
            <p class='text-sm font-semibold text-gray-800'>by:</p>
            <select name="" id="" class="w-[100px] text-[14px] cursor-pointer pr-2" v-model="selectedSearchOption">
                <option v-for="item in searchOption" :value="item.by" :key="item.id"><span>{{ item.title
                }}</span>
                </option>
            </select>
        </div>
    </div>
</template>