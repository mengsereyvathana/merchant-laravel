<script setup lang="ts">
import HeaderLayout from "./layout/HeaderLayout.vue";
import TruncatedPagination from "../components/TruncatedPagination.vue";
import { ref } from 'vue';

const open = ref(false); 
</script>

<template>
    <HeaderLayout />
    <div class="lg:ml-[250px]" @click="open = !open">
        <RouterView></RouterView>
    </div>
</template>
