<script setup lang="ts">
import Swal from 'sweetalert2';
import { slideshowService } from '../service/api/modules/slideshow.api';
import { computed } from 'vue'; //changed
import { onMounted, ref } from 'vue';
import { RouteParams, useRoute, useRouter } from 'vue-router';
import { ISlideshowItem } from '../types/Slideshow'
import { IFormSlideshow } from '../types/Form';

const router = useRouter();
const route = useRoute();
const params = computed<RouteParams>(() => route.params);

let currentSlideOrder = ref<number>(0);
let slideshows = ref<ISlideshowItem[]>([]);
let imagePreview = ref<string>('');
let loading = ref<boolean>(false);

let form = ref<IFormSlideshow>({
    order_number: 0,
    id: 0,
    title: '',
    tage: '',
    link: '',
    image: null,
    enable: false,
});

onMounted(async () => {
    getSlideshow();
    getSlideOrder();
});

const getSlideOrder = async () => {
    const [error, data] = await slideshowService.getAllSlideshows(0);
    if (error) console.log(error);
    else {
        slideshows.value = data.data as ISlideshowItem[];
        // if (slideshows.value.length > 0) {
        //     form.value.order_number = slideshows.value[0].slide_order;
        // }
    }
}

const getSlideshow = async () => {
    const [error, data] = await slideshowService.getSlideshow(Number(params.value.id))
    if (error) console.log(error)
    else {
        if (data.success) {
            const slideshowItem = data.data as ISlideshowItem
            console.log(slideshowItem)
            currentSlideOrder.value = slideshowItem.slide_order;
            form.value.order_number = slideshowItem.slide_order;
            form.value.id = slideshowItem.id;
            form.value.title = slideshowItem.title;
            form.value.tage = slideshowItem.tage;
            form.value.link = slideshowItem.link;
            form.value.enable = slideshowItem.action == '1' ? true : false;
            imagePreview.value = slideshowItem.image;
        }
    }
}

const updateSlideshow = async () => {
    console.log(form.value.order_number)
    if (form.value.title == "" || form.value.tage == "" || form.value.link == "" || form.value.order_number == null) return Swal.fire({
        toast: true,
        position: 'top',
        showClass: {
            icon: 'animated heartBeat delay-1s'
        },
        icon: 'error',
        text: 'Please check information again',
        showConfirmButton: false,
        timer: 1000
    });

    loading.value = true;

    const formData = new FormData();
    formData.append('id', form.value.id.toString())
    formData.append('new_title', form.value.title);
    formData.append('new_tage', form.value.tage);
    formData.append('new_link', form.value.link);
    formData.append('new_order', form.value.order_number.toString())
    formData.append('action', form.value.enable ? '1' : '0');
    formData.append('_method', 'PUT');

    if (form.value.image) {
        formData.append('new_image', form.value.image);
    }

    const [error, data] = await slideshowService.editSlideshow(Number(params.value.id), formData)
    if (error) console.log(error)
    else {
        if (data.success) {
            // Swal.fire({
            //     toast: true,
            //     position: 'top',
            //     showClass: {
            //         icon: 'animated heartBeat delay-1s'
            //     },
            //     icon: 'success',
            //     text: data.message,
            //     showConfirmButton: false,
            //     timer: 1000
            // }).then(() => {
            // })
            router.push('/admin/show_slideshow');
        }
    }
    loading.value = false;
}

function previewImage() {
    if (form.value.image) {
        return URL.createObjectURL(form.value.image);
    }
    return imagePreview.value;
}

function browseImage(e: Event) {
    const target = e.target as HTMLInputElement;
    const files: FileList | null = target.files;
    if (files && files.length > 0) {
        const file = files[0];
        const allowExtenstions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
        if (allowExtenstions.exec(target.value)) form.value.image = file;
    }
}

</script>

<template>
    <div>
        <div class="d-flex flex-row justify-space-between align-center flex-wrap mb-4">
            <h1 class='text-header font-weight-medium'>Edit a slideshow</h1>
            <v-btn @click="updateSlideshow()" color="success" :loading="loading" flat>
                Edit
            </v-btn>
        </div>
        <v-layout>
            <v-row>
                <v-col cols="12" md="8">
                    <div class="d-flex justify-space-between align-center">
                        <div class="font-weight-medium text-grey-darken-4">Title</div>
                        <div class="flex items-center gap-2">
                            <v-checkbox v-model="form.enable" label="enable" density="compact" color="success"
                                hide-details></v-checkbox>
                        </div>
                    </div>
                    <div>
                        <v-text-field v-model="form.title" density="compact" placeholder="Write title here..."
                            variant="outlined" hide-details></v-text-field>
                    </div>
                    <div class="mt-7">
                        <div class="mb-2 font-weight-medium text-grey-darken-4">Tag</div>
                        <v-textarea v-model="form.tage" density="compact" placeholder="Write tag here..." variant="outlined"
                            hide-details></v-textarea>
                    </div>
                    <div class="mt-7">
                        <div class="mb-2 font-weight-medium text-grey-darken-4">Link</div>
                        <v-text-field v-model="form.link" density="compact" placeholder="Write link here..."
                            variant="outlined" hide-details></v-text-field>
                    </div>
                    <div class="mt-7">
                        <div class="mb-2 font-weight-medium text-grey-darken-4">Display images</div>
                        <div class="gap-4 mt-1" v-if="imagePreview !== ''">
                            <v-img :src="previewImage()" aspect-ratio="1/1" :width="100" :height="100" alt="" cover></v-img>
                        </div>
                        <div
                            class="relative h-[200px] mt-3 border-dashed border-2 border-gray-300 rounded-lg flex flex-col justify-center items-center">
                            <v-icon>mdi-upload</v-icon>
                            <p class='font-weight-medium text-grey-darken-4'>Browse image</p>
                            <input class='absolute w-full h-full opacity-0 cursor-pointer' type="file" name="" id=""
                                accept="image/*" @change="browseImage" />
                        </div>
                    </div>
                </v-col>
                <v-col cols="12" md="4">
                    <div class="border-solid border border-gray-300 rounded-lg p-4 h-[305px] bg-white mt-2 md:mt-10">
                        <div class="d-flex flex-wrap justify-space-between align-center">
                            <div class='font-weight-medium text-grey-darken-4'>Current order: {{ currentSlideOrder }}</div>
                            <RouterLink :to="'/admin/add_slideshow'">
                                <v-btn color="blue" class='text-none' variant="tonal" flat>Add
                                    slideshow
                                </v-btn>
                            </RouterLink>
                        </div>
                        <select name="" id="" class="w-full mt-4 text-[14px] cursor-pointer" v-model="form.order_number">
                            <option v-for="item in slideshows" :value="item.slide_order" :key="item.id">{{
                                item.slide_order
                            }}
                            </option>
                        </select>
                    </div>
                </v-col>
            </v-row>
        </v-layout>
    </div>
</template>