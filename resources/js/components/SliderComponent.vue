<script setup lang="ts">
import { ref, onMounted } from 'vue';
import { Swiper, SwiperSlide } from 'swiper/vue';
import { ISliderItem } from '@/types/Slider';
import 'swiper/css';
import 'swiper/css/pagination';
import 'swiper/css/navigation';
import 'swiper/css/effect-coverflow';

import { EffectCoverflow, Autoplay, Pagination } from 'swiper/modules';
import { sliderService } from '@/services/api/modules/slider.api';

const modules = [EffectCoverflow, Autoplay, Pagination];

let sliders = ref<ISliderItem[]>([])
let isSliderLoaded = ref<boolean>(false);

onMounted(() => {
    getSlider();
})

const getSlider = async () => {
    const [error, data] = await sliderService.getAllSliders();
    if (error) console.log(error);
    else {
        if (data.success) {
            sliders.value = data.data;
            isSliderLoaded.value = true;
        }
    }
}

</script>
<template>
    <swiper v-if="isSliderLoaded" effect="coverflow" :grabCursor="true" :slidesPerView="'auto'" :loop="true"
        :centeredSlides="true" :coverflowEffect="{ rotate: 0, stretch: -20, depth: 100, modifier: 1.5, slideShadows: true }"
        :pagination="{ clickable: true }" :modules="modules" class="swiper-container"
        :style="{ '--swiper-pagination-color': '#024EA7' }">
        <swiper-slide v-for="item in sliders" :key="item.id">
            <img :src="item.image" alt="">
        </swiper-slide>
    </swiper>
    <div class="h-screen flex mt-10 justify-center" v-else>
        <v-progress-circular indeterminate color="main"></v-progress-circular>
    </div>
</template>
<style scoped>
.swiper {
    overflow: hidden !important;
    padding: 0 50px;
}

.swiper-container {
    width: 100%;
    height: 200px;
    padding: 30px;
    padding-top: 0;
}

.swiper-slide {
    border-radius: 2rem;
    overflow: hidden;
}

.swiper-slide img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.swiper-pagination {
    transform: translate3d(0, 30px, 0) !important;
}
</style>
