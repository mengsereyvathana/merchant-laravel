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
        <v-layout>
            <v-responsive>
                <v-row>
                    <v-col cols="12" xs="6" sm="5" md="4" lg="3">
                        <v-text-field @keyup="search()" v-model="searchQuery" density="compact" variant="solo"
                            placeholder="Search" prepend-inner-icon="mdi-magnify" hide-details bg-color="transparent"
                            :flat="true" class="border border-solid rounded-md" block></v-text-field>
                    </v-col>
                    <v-col cols="12" xs="6" sm="5" md="4" lg="3" class="d-flex">
                        <v-select v-model="selectedSearchOption" :items="searchOption" item-value="by" density="compact"
                            variant="solo" hide-details :flat="true" clearable
                            class="border border-solid rounded-md"></v-select>
                    </v-col>
                </v-row>
            </v-responsive>
        </v-layout>
    </div>
</template>