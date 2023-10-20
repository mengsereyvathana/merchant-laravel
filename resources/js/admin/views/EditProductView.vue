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

const updateProduct = async () => {
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
            <h1 class='text-header font-weight-medium'>Edit a product</h1>
            <v-btn @click="updateProduct()" color="success" flat>
                publish
            </v-btn>
        </div>
        <v-layout>
            <v-row>
                <v-col cols="12" md="8">
                    <div class="d-flex justify-space-between align-center">
                        <div class="font-weight-medium text-grey-darken-4">Product Name</div>
                        <div class="flex items-center gap-2">
                            <v-checkbox v-model="form.enable" label="enable" density="compact" color="success"
                                hide-details></v-checkbox>
                        </div>
                    </div>
                    <div>
                        <v-text-field v-model="form.name" density="compact" placeholder="Write name here..."
                            variant="outlined" hide-details></v-text-field>
                    </div>
                    <div class="mt-7">
                        <div class="mb-2 font-weight-medium text-grey-darken-4">Description</div>
                        <v-text-field v-model="form.description" density="compact" placeholder="Write description here..."
                            variant="outlined" hide-details></v-text-field>
                    </div>
                    <div class="mt-7 p-3 border border-solid rounded-md">
                        <div class="d-flex flex-column flex-md-row justify-space-between">
                            <div class="w-100 mr-3">
                                <div class="mb-2 font-weight-medium text-grey-darken-4">Price</div>
                                <v-text-field v-model="form.price" density="compact" placeholder="Write price here..."
                                    variant="outlined" hide-details></v-text-field>
                            </div>
                            <div class="w-100 mt-3 mt-md-0">
                                <div class="mb-2 font-weight-medium text-grey-darken-4">Buy</div>
                                <v-text-field v-model="form.buy" density="compact" placeholder="Write buy here..."
                                    variant="outlined" hide-details></v-text-field>
                            </div>
                        </div>
                        <div class="mt-3 d-flex flex-column flex-md-row justify-space-between">
                            <div class="w-100 mr-3">
                                <div class="mb-2 font-weight-medium text-grey-darken-4">Ram</div>
                                <v-text-field v-model="form.ram" density="compact" placeholder="Write ram here..."
                                    variant="outlined" hide-details></v-text-field>
                            </div>
                            <div class="w-100 mt-3 mt-md-0">
                                <div class="mb-2 font-weight-medium text-grey-darken-4">Storage</div>
                                <v-text-field v-model="form.storage" density="compact" placeholder="Write storage here..."
                                    variant="outlined" hide-details></v-text-field>
                            </div>
                        </div>
                        <div class="mt-3 w-100 mr-3">
                            <div class="mb-2 font-weight-medium text-grey-darken-4">Stock</div>
                            <v-text-field v-model="form.stock" density="compact" placeholder="Write stock here..."
                                variant="outlined" hide-details></v-text-field>
                        </div>
                    </div>
                    <div class="mt-7">
                        <div class="mb-2 font-weight-medium text-grey-darken-4">Display images</div>
                        <div class="gap-4 mt-1" v-if="imagePreview != ''">
                            <v-img :src="previewImage()" aspect-ratio="1/1" :width="100" :height="100" alt="" cover></v-img>
                        </div>
                        <div
                            class="relative h-[200px] mt-3 border-dashed border-2 border-gray-300 rounded-lg flex flex-col justify-center items-center">
                            <v-icon>mdi-upload</v-icon>
                            <p class='text-body-2 font-weight-medium text-grey-darken-4'>Browse slideshow image</p>
                            <input class='absolute w-full h-full opacity-0 cursor-pointer' type="file" name="" id=""
                                accept="image/*" @change="browseImage" />
                        </div>
                    </div>
                </v-col>
                <v-col cols="12" md="4">
                    <div class="border-solid border border-gray-300 rounded-lg p-4 h-[305px] bg-white mt-2 md:mt-10">
                        <div class="d-flex flex-wrap justify-space-between align-center">
                            <div class='font-weight-medium text-grey-darken-4'>Category</div>
                            <RouterLink :to="'/admin/add_category'">
                                <v-btn color="blue" class='text-none' variant="tonal" flat>Add
                                    category
                                </v-btn>
                            </RouterLink>
                        </div>
                        <select name="" id="" class="w-full mt-4 text-[14px] cursor-pointer" v-model="form.category.id">
                            <option v-for="item in categories" :value="item.id" :key="item.id">{{ item.name }}
                            </option>
                        </select>
                        <div class="mt-7 d-flex flex-wrap justify-space-between align-center">
                            <div class='font-weight-medium text-grey-darken-4'>Color</div>
                        </div>
                        <!-- <select name="" id="" class="w-full mt-4 text-[14px] cursor-pointer" v-model="form.color_id">
                            <option v-for="item in colors" :value="item.id" :key="item.id">{{ item.name }}</option>
                        </select> -->
                    </div>
                </v-col>
            </v-row>
        </v-layout>
    </div>
</template>