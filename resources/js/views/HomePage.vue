<script setup lang="ts">
import Slider from '../components/SliderComponent.vue'
import Trending from '../components/TrendingComponent.vue'
import Search from '../components/SearchComponent.vue'
import Nav from '../components/NavigateComponent.vue'
</script>
<template>
    <div class="flex flex-col gap-3 bg-white">
        <div class="mb-[74px]">
            <Search />
            <Slider />
            <Trending />
        </div>
        <Nav />
    </div>
</template>