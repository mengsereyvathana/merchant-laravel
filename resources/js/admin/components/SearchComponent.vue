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
let searchOption = ref<ISearchOption[]>(props.searchOption);

onMounted(async () => {
    selectedSearchOption.value = searchOption.value[0].by;
});


const search = () => {
    emits('currentSearchUpdated', searchQuery.value, selectedSearchOption.value);
};

</script>

<template>
    <div class="d-flex flex-row">
        <!-- <input @keyup="search()" v-model="searchQuery" type="text" name="" id="" placeholder='Search'
                class='text-sm pl-3 w-full lg:w-[180px] h-[40px] rounded-md border-gray-300 border-solid border focus:border-current focus:ring-current' /> -->
        <v-layout>
            <v-responsive max-width="450">
                <v-row>
                    <v-col>
                        <v-text-field @keyup="search()" v-model="searchQuery" density="compact" variant="solo"
                            placeholder="Search" prepend-inner-icon="mdi-magnify" hide-details bg-color="transparent"
                            :flat="true" class="border border-solid rounded-md" block></v-text-field>
                    </v-col>
                    <v-col class="d-flex">
                        <span class="align-self-center mr-2 font-weight-light text-grey-darken-1">
                            Filter by
                        </span>
                        <v-select v-model="selectedSearchOption" :items="searchOption" item-value="by" density="compact"
                            variant="solo" hide-details :flat="true" clearable
                            class="border border-solid rounded-md"></v-select>
                    </v-col>
                </v-row>
            </v-responsive>
            <!-- <img :src="Upload.icon('search.svg')" alt=""
                class="absolute top-[50%] left-3 translate-y-[-50%] w-4 h-4" /> -->
            <!-- <div class="flex flex-row justify-between items-center gap-3">
                    <p class='text-sm font-semibold text-gray-800'>by:</p>
                    <select name="" id="" class="w-[100px] text-[14px] cursor-pointer pr-2" v-model="selectedSearchOption">
                        <option v-for="item in searchOption" :value="item.by" :key="item.id"><span>{{ item.title
                        }}</span>
                    </option>
                </select>
            </div> -->
        </v-layout>
    </div>
</template>