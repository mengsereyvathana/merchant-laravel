<script setup lang="ts">
import Swal from 'sweetalert2';
import { slideshowService } from '../service/api/modules/slideshow.api';
import { computed } from '@vue/reactivity';
import { onMounted, ref } from 'vue';
import { RouteParams, useRoute, useRouter } from 'vue-router';
import { ISlideshowItem } from '../types/Slideshow'
import { API_URL } from '@/config/api.config';
import { ICategoryItem } from '../types/Category';
import { categoryService } from '../service/api/modules/category.api';

const router = useRouter();
const route = useRoute();
const params = computed<RouteParams>(() => route.params)

interface IForm {
    name: string;
    description: string;
    image: File | null;
    enable: boolean;
}

let form = ref<IForm>({
    name: '',
    description: '',
    image: null,
    enable: false,
});
let imagePreview = ref<string>('');

onMounted(async () => {
    getCategory();
});

const getCategory = async () => {
    const [error, data] = await categoryService.getCategory(Number(params.value.id))
    if (error) console.log(error)
    else {
        if (data.success) {
            const item = data.data as ICategoryItem
            form.value.name = item.name;
            form.value.description = item.description;
            form.value.enable = item.action == '1' ? true : false;
            imagePreview.value = item.image;
        }
    }
}

const updateCategory = async () => {
    if (form.value.name === "" || form.value.description === "") return Swal.fire({
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
    formData.append('name', form.value.name);
    console.log(form.value.description)
    formData.append('des', form.value.description);
    if (form.value.image) {
        formData.append('image', form.value.image);
    }
    formData.append('action', form.value.enable ? '1' : '0');
    formData.append('_method', 'PUT');

    const [error, data] = await categoryService.editCategory(Number(params.value.id), formData)
    if (error) console.log(error)
    else {
        console.log(data)
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
                router.push('/admin/show_category');
            })
        }
    }
}

function previewImage() {
    if (form.value.image) {
        return URL.createObjectURL(form.value.image);
    }
    return API_URL + imagePreview.value;
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
            <h1 class='text-xl font-bold text-black_500'>Edit a category</h1>
            <button class='px-4 py-3 rounded-md bg-primary text-white text-sm cursor-pointer' @click="updateCategory()">Edit
                category</button>
        </div>
        <div class="mt-8 flex flex-col gap-8 md:flex-row shadow-[#E1E1E1_0px_1px_8px] p-6 rounded-md">
            <div class='flex-[4]'>
                <div class="flex justify-between">
                    <h1 class='text-xl font-semibold text-gray-800'>Name</h1>
                    <div class="flex items-center gap-2">
                        <input v-model="form.enable" type="checkbox" id="enable"
                            class="accent-current border border-solid border-gray-300 rounded-sm cursor-pointer focus:ring-primary">
                        <label for="enable">Enable</label>
                    </div>
                </div>
                <div class="mt-3">
                    <input type="text" name="" id="" class='input text-sm w-full' placeholder='Write name here...'
                        v-model="form.name" />

                </div>
                <div class="mt-7">
                    <h1 class='text-xl font-semibold text-gray-800'>Description</h1>
                    <div class='mt-3'>
                        <textarea name="" id="" placeholder='Write a description here...'
                            class='text-sm h-[200px] input w-full resize-none' v-model="form.description"></textarea>
                    </div>
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
        </div>

    </div>
</template>