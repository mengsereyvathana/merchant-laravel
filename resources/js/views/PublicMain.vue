<script setup lang="ts">
import { ref, watch, computed, onMounted } from 'vue'
import Header from "./layout/HeaderLayout.vue";
import { transition } from '@/store/transition';

const tranName = ref<string>("")

onMounted(() => {
    tranName.value = transitionName.value
})

let transitionName = computed<string>(() => {
    return transition.value;
})

const scrollToTop = () => {
    window.scrollTo({ top: 0, behavior: "smooth" });
};

watch([transitionName],
    () => { scrollToTop(); },
    { immediate: true }
);
</script>

<template>
    <div class="max-h-screen">
        <Header />
        <div class=" bg-white min-h-screen h-screen bg-center relative">
            <div class="absolute top-[60px] left-0 right-0">
                <RouterView v-slot="{ Component }">
                    <Transition :name="transitionName === 'slide-left' ? 'page-left' : 'page-right'">
                        <component :is="Component" />
                    </Transition>
                </RouterView>
            </div>
        </div>
    </div>
    <!-- <Footer /> -->
</template>
<style>
.page-left-enter-active,
.page-right-enter-active,
.page-left-leave-active,
.page-right-leave-active {
    position: fixed;
    top: 60px;
    left: 0;
    right: 0;
    bottom: 0;
    margin-top: 0;
    transition: all .5s ease-in-out;
}

.page-left-enter-from,
.page-right-leave-to {
    transform: translateX(100%);
    opacity: 1;
    z-index: 99;
}

.page-left-leave-to,
.page-right-enter-from {
    opacity: 0;
    transform: scale(0.95)
}

.page-left-enter-to,
.page-right-enter-to {
    transform: translateX(0);
}
</style>
