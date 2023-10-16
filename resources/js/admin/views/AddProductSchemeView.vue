<script setup lang="ts">
import Swal from 'sweetalert2';
import { computed, onMounted, ref } from 'vue';
import { useRouter, useRoute, RouteParams } from 'vue-router';
import { productSchemeService } from '../service/api/modules/product-scheme.api'
import { IProductItem } from '../types/Product';
import { productService } from '../service/api/modules/product.api';
import { API_URL } from '../config/api.config';

const router = useRouter();
const route = useRoute();
const params = computed<RouteParams>(() => route.params)
let imagePreview = ref<string | undefined>("");

interface SchemeList {
    id: number;
    name: string;
}
let schemeList: SchemeList[] = [
    {
        id: 1,
        name: 'user1',
    },
    {
        id: 2,
        name: 'user2',
    }
];

onMounted(() => {
    getScheme();
    getProduct();
});

interface IForm {
    scheme_id: number;
    unit_price: number | null;
    products: IProductItem | null;

}

let form = ref<IForm>({
    scheme_id: 0,
    unit_price: null,
    products: null,
});

const getScheme = async () => {
    // const [error, data] = await categoryService.getAllCategories();
    // if (error) console.log(error);
    // else {
    //     categories.value = data.data;
    //     if (categories.value.length > 0) form.value.c_id = categories.value[0].id;
    // }
    form.value.scheme_id = schemeList[0].id;
}

const getProduct = async () => {
    const [error, data] = await productService.getProduct(Number(params.value.id))
    if (error) console.log(error);
    else {
        if (data.success) {
            const productItem = data.data as IProductItem;
            form.value.products = productItem;
            imagePreview.value = productItem.image;
        }
    }
}


const saveProduct = async () => {
    if (form.value.scheme_id === 0 || form.value.unit_price === null) return Swal.fire({
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
    formData.append('pro_id', route.params.id.toString());
    formData.append('scheme_id', form.value.scheme_id.toString());
    formData.append('unit_price', form.value.unit_price.toString());

    const [error, data] = await productSchemeService.createProductScheme(formData)
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
                router.push("/admin/show_product_scheme");
            })
        }
        else {
            Swal.fire({
                toast: true,
                position: 'top',
                showClass: {
                    icon: 'animated heartBeat delay-1s'
                },
                icon: 'error',
                text: data.message,
                showConfirmButton: false,
                timer: 1000
            })
        }
    }
}

function previewImage() {
    return API_URL + imagePreview.value;
}
</script>

<template>
    <div class='lg:py-6 lg:px-8 p-5'>
        <div class="flex justify-between items-end rounded-md">
            <h1 class='text-xl font-bold text-black_500'>Add a product scheme</h1>
            <button class='px-4 py-3 rounded-md bg-primary text-white text-sm cursor-pointer'
                @click="saveProduct()">Publish</button>
        </div>
        <div class="mt-8 flex flex-col gap-8 md:flex-row shadow-[#E1E1E1_0px_1px_8px] p-6 rounded-md">
            <div class='flex-[4]'>
                <div class="flex justify-between">
                    <h1 class='text-xl font-semibold text-gray-800'>Unit Price</h1>
                    <div class="flex items-center gap-2">
                        <!-- <input v-model="form.id" type="checkbox" id="enable"
                            class="accent-current border border-solid border-gray-300 rounded-sm cursor-pointer focus:ring-primary">
                        <label for="enable">Enable</label> -->
                    </div>
                </div>
                <div class="mt-3">
                    <input type="text" name="" id="" class='input text-sm w-full' placeholder='Write unit price here...'
                        v-model="form.unit_price" />
                </div>

                <div class="flex justify-between mt-7">
                    <h1 class='text-xl font-semibold text-gray-800'>Product Select :</h1>
                </div>
                <div class="border border-solid border-gray-300 rounded-lg p-4 bg-white mt-3" v-if="form.products">
                    <div class="flex flex-row md:flex-row items-center justify-between gap-4">
                        <div class="flex flex-row gap-4 w-full">
                            <div class='w-full'>
                                <p class='mb-2 text-gray-800 text-[15px] font-semibold'>Name</p>
                                <input type="text" placeholder='$$$' class='input w-full opacity-70' disabled
                                    v-model="form.products.name" />
                            </div>
                            <!-- <div class="flex items-center gap-2 mt-7">
                                <input v-model="form.enable" type="checkbox" id="enable"
                                    class="w-[30px] h-[20px] accent-current border border-solid border-gray-300 rounded-sm cursor-pointer focus:ring-primary"
                                    disabled>
                                <label for="enable">Enable</label>
                            </div> -->
                            <!-- <div class='w-full'>
                                <p class='mb-2 text-gray-800 text-[15px] font-semibold'>Price</p>
                                <input type="text" placeholder='$$$' class='input w-full' v-model="form.products.price" />
                            </div> -->
                        </div>
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
                    </div>

                    <div class="grid grid-cols-2 gap-4 mt-3">
                        <div class='w-full'>
                            <p class='mb-2 text-gray-800 text-[15px] font-semibold'>Price</p>
                            <input type="text" placeholder='...' class='input w-full opacity-70' disabled
                                v-model="form.products.price" />
                        </div>
                        <div class='w-full'>
                            <p class='mb-2 text-gray-800 text-[15px] font-semibold'>Stock</p>
                            <input type="text" placeholder='...' class='input w-full opacity-70' disabled
                                v-model="form.products.stock" />
                        </div>
                        <div class='w-full'>
                            <p class='mb-2 text-gray-800 text-[15px] font-semibold'>Ram</p>
                            <input type="text" placeholder='...' class='input w-full opacity-70' disabled
                                v-model="form.products.ram" />
                        </div>
                        <div class='w-full'>
                            <p class='mb-2 text-gray-800 text-[15px] font-semibold'>Storage</p>
                            <input type="text" placeholder='...' class='input w-full opacity-70' disabled
                                v-model="form.products.storage" />
                        </div>
                    </div>
                    <div class='w-full mt-4'>
                        <p class='mb-2 text-gray-800 text-[15px] font-semibold'>Color</p>
                        <input type="text" placeholder='...' class='input w-full opacity-70' disabled
                            v-model="form.products.color" />
                    </div>

                </div>
                <!-- <div class="mt-7">
                    <h1 class='text-xl font-semibold text-gray-800'>Tag</h1>
                    <div class='mt-3'>
                        <textarea name="" id="" placeholder='Write a text here...'
                            class='text-sm h-[200px] input w-full resize-none' v-model="form.scheme_id"></textarea>
                    </div>
                </div>
                <div class="flex justify-between">
                    <h1 class='text-xl font-semibold text-gray-800'>Link</h1>
                </div>
                <div class="mt-3">
                    <input type="text" name="" id="" class='input text-sm w-full' placeholder='Write link here...'
                        v-model="form.unit_price" />
                </div> -->
            </div>
            <div class='flex-[2]'>
                <div class="border-solid border border-gray-300 rounded-lg p-4 h-[305px] bg-white mt-2 md:mt-10">
                    <div class="flex justify-between items-center">
                        <p class='text-xl font-semibold text-gray-800'>Product Scheme</p>
                        <!-- <RouterLink :to="'/admin/add_category'">
                            <p class='text-sm cursor-pointer font-semibold text-primary hover:underline'>Add new category
                            </p>
                        </RouterLink> -->
                    </div>
                    <select name="" id="" class="w-full mt-4 text-[14px] cursor-pointer" v-model="form.scheme_id">
                        <option v-for="item in schemeList" :value="item.id" :key="item.id">{{ item.name }}</option>
                    </select>
                </div>
            </div>
        </div>
    </div>
</template>