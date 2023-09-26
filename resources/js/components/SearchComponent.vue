<script setup lang="ts">
import { ref } from 'vue'
import _ from "lodash"
import { Upload } from '@/services/helper/index';
import { useStore } from '@/use/useStore';
import { SEARCH_STORE } from '@/store/constants';

let searchQuery = ref<string>('');
const store = useStore();
const searchProduct = _.debounce(async () => {
    store.dispatch(SEARCH_STORE.ACTIONS.SEARCH_PRODUCT, searchQuery.value)
}, 500);

</script>
<template>
    <div class="p-3">
        <div class="relative">
            <input @keyup="searchProduct()" v-model="searchQuery" type="text" name="" id=""
                placeholder="Search for items..."
                class="ripple-effect text-sm pl-4 pr-10 w-full h-[48px] rounded-md border-gray-300 border-solid border focus:border-main focus:ring-main" />
            <img :src="Upload.icon('search.svg')" alt="" class="absolute top-[50%] right-3 translate-y-[-50%] w-4 h-4" />
        </div>
    </div>
</template>