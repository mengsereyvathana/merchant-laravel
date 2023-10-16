<script setup lang="ts">
import Swal from 'sweetalert2';
import { computed } from '@vue/reactivity';
import { onMounted, ref } from 'vue';
import { productService } from '../service/api/modules/product.api'
import { RouteParams, useRoute, useRouter } from 'vue-router';
import { IProductItem } from '../types/Product';
import { ICategoryItem } from '../types/Category';
import { categoryService } from '../service/api/modules/category.api';
import { API_URL } from '@/config/api.config';

const router = useRouter();
const route = useRoute();
const params = computed<RouteParams>(() => route.params)

interface IForm {
    category: Partial<ICategoryItem>;
    name: string;
    description: string;
    price: number;
    buy: number;
    margin: number;
    stock: number;
    ram: string;
    storage: string;
    color: string;
    enable: boolean;
    image: File | null;
}
let form = ref<IForm>({
    category: {},
    name: '',
    description: '',
    price: 0,
    buy: 0,
    margin: 0,
    stock: 0,
    ram: '',
    storage: '',
    color: '',
    enable: false,
    image: null
});
let categories = ref<ICategoryItem[]>([]);
let imagePreview = ref("");

onMounted(async () => {
    getProduct();
    getCategory();
});

const getCategory = async () => {
    const [error, data] = await categoryService.getAllCategories(0);
    if (error) console.log(error);
    else {
        const item = data.data as ICategoryItem[];
        categories.value = item;
        // console.log("hello")
        // if (categories.value.length > 0) form.value.category = categories.value;
    }
}

const getProduct = async () => {
    const [error, data] = await productService.getProduct(Number(params.value.id))
    if (error) console.log(error);
    else {
        if (data.success) {
            const item = data.data as IProductItem;
            if (item.category) {
                form.value.category = item.category
            }
            form.value.name = item.name;
            form.value.description = item.description;
            form.value.price = item.price;
            form.value.buy = item.buy;
            form.value.stock = item.stock;
            form.value.ram = item.ram;
            form.value.storage = item.storage;
            form.value.color = item.color;
            form.value.enable = item.action == '1' ? true : false;
            imagePreview.value = item.image;
        }
    }
}

const updateSlideshow = async () => {
    if (form.value.name == "" || form.value.description == "" || form.value.price === 0 || form.value.buy === 0 || form.value.stock === 0 || form.value.ram == "" || form.value.storage == "" || form.value.color == "" || form.value.category.id === undefined) return Swal.fire({
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
        formData.append('image', form.value.image);
    }
    formData.append('category_id', form.value.category.id.toString());
    formData.append('name', form.value.name);
    formData.append('description', form.value.description);
    formData.append('price', form.value.price.toString());
    formData.append('buy', form.value.buy.toString());
    formData.append('stock', form.value.stock.toString());
    formData.append('ram', form.value.ram);
    formData.append('storage', form.value.storage);
    formData.append('color', form.value.color);
    formData.append('action', form.value.enable ? '1' : '0');
    formData.append('_method', "PUT");

    const [error, data] = await productService.editProduct(Number(params.value.id), formData)
    if (error) console.log(error);
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
                router.push('/admin/show_product');
            });
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
            <h1 class='text-xl font-bold text-black_500'>Edit a product</h1>
            <button class='px-4 py-3 rounded-md bg-primary text-white text-sm cursor-pointer'
                @click="updateSlideshow()">Edit product</button>
        </div>
        <div class="mt-8 flex flex-col gap-8 md:flex-row shadow-[#E1E1E1_0px_1px_8px] p-6 rounded-md">
            <div class='flex-[4]'>
                <div class="flex justify-between">
                    <h1 class='text-xl font-semibold text-gray-800'>Product Name</h1>
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
                    <h1 class='text-xl font-semibold text-gray-800'>Product Description</h1>
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
                                <!-- delete -->
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

                <div class="border border-solid border-gray-300 rounded-lg p-4 mt-8 bg-white">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                        <div class='w-full'>
                            <p class='mb-2 text-gray-800 text-[15px] font-semibold'>Price</p>
                            <input type="text" placeholder='$$$' class='input w-full' v-model="form.price" />
                        </div>
                        <div class='w-full'>
                            <p class='mb-2 text-gray-800 text-[15px] font-semibold'>Buy</p>
                            <input type="text" placeholder='$$$' class='input w-full' v-model="form.buy" />
                        </div>
                    </div>
                    <div class='w-full mt-4'>
                        <p class='mb-2 text-gray-800 text-[15px] font-semibold'>Add to stock</p>
                        <input type="text" placeholder='Quantity' class='input w-full' v-model="form.stock" />
                    </div>
                    <div class="flex flex-col md:flex-row items-center justify-between gap-4 mt-4">
                        <div class='w-full'>
                            <p class='mb-2 text-gray-800 text-[15px] font-semibold'>Ram</p>
                            <input type="text" placeholder='...' class='input w-full' v-model="form.ram" />
                        </div>
                        <div class='w-full'>
                            <p class='mb-2 text-gray-800 text-[15px] font-semibold'>Storage</p>
                            <input type="text" placeholder='...' class='input w-full' v-model="form.storage" />
                        </div>
                    </div>
                    <div class='w-full mt-4'>
                        <p class='mb-2 text-gray-800 text-[15px] font-semibold'>Color</p>
                        <input type="text" placeholder='Quantity' class='input w-full' v-model="form.color" />
                    </div>
                </div>
            </div>

            <div class='flex-[2]'>
                <div class="border-solid border border-gray-300 rounded-lg p-4 h-[305px] bg-white mt-2 md:mt-10">
                    <div class="flex justify-between items-center">
                        <p class='text-xl font-semibold text-gray-800'>Category</p>
                        <RouterLink :to="'/admin/add_category'">
                            <p class='text-sm cursor-pointer font-semibold text-primary hover:underline'>Add new category
                            </p>
                        </RouterLink>
                    </div>
                    <select name="" id="" class="w-full mt-4 text-[14px] cursor-pointer" v-model="form.category.id">
                        <option v-for="item in categories" :value="item.id" :key="item.id">{{ item.name }}</option>
                    </select>
                </div>
            </div>
        </div>

    </div>
</template>