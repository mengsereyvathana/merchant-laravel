<script setup lang="ts">
import HeaderLayout from "./layout/HeaderLayout.vue";
import SidebarLayout from "./layout/SidebarLayout.vue";
</script>

<template>
    <v-layout>
        <SidebarLayout />
        <v-main>
            <div class="lg:py-3 lg:px-5 p-3">
                <HeaderLayout />
                <RouterView></RouterView>
            </div>
        </v-main>
    </v-layout>
</template>
