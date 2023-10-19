<script setup lang="ts">
import Swal from 'sweetalert2';
import { slideshowService } from '../service/api/modules/slideshow.api';
import { computed } from 'vue'; //changed
import { onMounted, ref } from 'vue';
import { RouteParams, useRoute, useRouter } from 'vue-router';
import { ISlideshowItem } from '../types/Slideshow'
import { API_URL } from '@/config/api.config';

const router = useRouter();
const route = useRoute();
const params = computed<RouteParams>(() => route.params)

interface IForm {
    order_number: number;
    id: number;
    title: string;
    tage: string;
    link: string;
    image: File | null;
    enable: boolean;
}

let form = ref<IForm>({
    order_number: 0,
    id: 0,
    title: '',
    tage: '',
    link: '',
    image: null,
    enable: false,
});
let currentSlideOrder = ref<number>(0)
let slideshows = ref<ISlideshowItem[]>([]);
let imagePreview = ref<string>('');

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
    if (form.value.title == "" || form.value.tage == "" || form.value.link == "") return Swal.fire({
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

    const formData = new FormData();
    if (form.value.image) {
        formData.append('new_image', form.value.image);
    }
    formData.append('id', form.value.id.toString())
    formData.append('new_title', form.value.title);
    formData.append('new_tage', form.value.tage);
    formData.append('new_link', form.value.link);
    formData.append('new_order', form.value.order_number.toString())
    formData.append('action', form.value.enable ? '1' : '0');
    formData.append('_method', 'PUT');

    console.log(Number(params.value.id))
    const [error, data] = await slideshowService.editSlideshow(Number(params.value.id), formData)
    if (error) console.log(error)
    else {
        if (data.success) {
            Swal.fire({
                toast: true,
                position: 'top',
                showClass: {
                    icon: 'animated heartBeat delay-1s'
                },
                icon: 'success',
                text: data.message,
                showConfirmButton: false,
                timer: 1000
            }).then(() => {
                router.push('/admin/show_slideshow');
            })
        }
    }
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
    <div class='lg:py-7 lg:px-10 p-5'>
        <div class="flex justify-between items-end">
            <h1 class='text-xl font-bold text-black_500'>Edit a slideshow</h1>
            <button class='px-4 py-3 rounded-md bg-primary text-white text-sm cursor-pointer'
                @click="updateSlideshow()">Edit
                slideshow</button>
        </div>
        <div class="mt-8 flex flex-col gap-8 md:flex-row shadow-[#E1E1E1_0px_1px_8px] p-6 rounded-md">
            <div class='flex-[4]'>
                <div class="flex justify-between">
                    <h1 class='text-xl font-semibold text-gray-800'>Title</h1>
                    <div class="flex items-center gap-2">
                        <input v-model="form.enable" type="checkbox" id="enable"
                            class="accent-current border border-solid border-gray-300 rounded-sm cursor-pointer focus:ring-primary">
                        <label for="enable">Enable</label>
                    </div>
                </div>
                <div class="mt-3">
                    <input type="text" name="" id="" class='input text-sm w-full' placeholder='Write title here...'
                        v-model="form.title" />

                </div>
                <div class="mt-7">
                    <h1 class='text-xl font-semibold text-gray-800'>Slideshow Tag</h1>
                    <div class='mt-3'>
                        <textarea name="" id="" placeholder='Write a text here...'
                            class='text-sm h-[200px] input w-full resize-none' v-model="form.tage"></textarea>
                    </div>
                </div>
                <div class="flex justify-between">
                    <h1 class='text-xl font-semibold text-gray-800'>Link</h1>
                </div>
                <div class="mt-3">
                    <input type="text" name="" id="" class='input text-sm w-full' placeholder='Write link here...'
                        v-model="form.link" />
                </div>
                <div class="mt-7">
                    <h1 class='text-xl font-semibold text-gray-800'>Display images</h1>

                    <div class="inline-flex gap-4 mt-2" v-if="imagePreview != ''">
                        <div
                            class='relative w-[100px] h-[100px] p-[2px] rounded-lg border-solid border border-gray-300 overflow-hidden'>
                            <div class='absolute right-[2px] top-[2px] cursor-pointer'>
                                <!-- <IoClose color='white' class='bg-red-500 w-4 h-4 rounded-full p-[0.1rem]' /> -->
                            </div>
                            <img :src="previewImage()" alt="" class='w-full h-full rounded-lg object-cover' />
                        </div>
                    </div>

                    <div
                        class="relative h-[200px] mt-3 border-dashed border-2 border-gray-300 rounded-lg flex flex-col justify-center items-center">
                        <i class="fas fa-cloud-upload text-primary text-[46px]"></i>
                        <p class='text-[15px] text-gray-600'>Browse slideshow image</p>
                        <input class='absolute w-full h-full opacity-0 cursor-pointer' type="file" name="" id=""
                            accept="image/*" @change="browseImage" />
                    </div>
                </div>
            </div>
            <div class='flex-[2]'>
                <div class="border-solid border border-gray-300 rounded-lg p-4 h-[305px] bg-white mt-2 md:mt-10">
                    <div class="flex justify-between items-center">
                        <p class='text-xl font-semibold text-gray-800'>Current order: {{ currentSlideOrder }}</p>
                        <RouterLink :to="'/admin/add_category'">
                            <p class='text-sm cursor-pointer font-semibold text-primary hover:underline'>Add new slideshow
                            </p>
                        </RouterLink>
                    </div>
                    <select name="" id="" class="w-full mt-4 text-[14px] cursor-pointer" v-model="form.order_number">
                        <option v-for="item in slideshows" :value="item.slide_order" :key="item.id">{{ item.slide_order }}
                        </option>
                    </select>
                </div>
            </div>
        </div>

    </div>
</template>