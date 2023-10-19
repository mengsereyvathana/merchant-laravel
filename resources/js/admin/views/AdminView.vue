<script setup lang="ts">
import HeaderLayout from "./layout/HeaderLayout.vue";
import SidebarLayout from "./layout/SidebarLayout.vue";
</script>

<template>
    <v-layout>
        <SidebarLayout />
        <v-main>
            <HeaderLayout />
            <RouterView></RouterView>
        </v-main>
    </v-layout>
</template>
