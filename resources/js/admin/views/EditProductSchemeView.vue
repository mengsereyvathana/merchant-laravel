<script setup lang="ts">
import Swal from 'sweetalert2';
import _ from "lodash";
import { computed } from '@vue/reactivity';
import { onMounted, ref } from 'vue';
import { productService } from '../service/api/modules/product.api'
import { RouteParams, useRoute, useRouter } from 'vue-router';
import { IProductItem } from '../types/Product';
import { ICategoryItem } from '../types/Category';
import { categoryService } from '../service/api/modules/category.api';
import { API_URL } from '@/config/api.config';
import { IProductSchemeItem, Products } from '../types/ProductScheme';
import { productSchemeService } from '../service/api/modules/product-scheme.api';
import { Upload } from '../service/helpers';

const router = useRouter();
const route = useRoute();
const params = computed<RouteParams>(() => route.params)

//Tooltip & copy
const productIdCopy = ref<HTMLSpanElement>();
const tooltipCopy = ref<HTMLButtonElement>();

let products = ref<IProductItem[]>([])

//search
let selectedSearchOption = ref("");
let searchQuery = ref();
let searchOption = ref(["name", "scheme id", "product id"]);

const header: string[] = ['PRODUCT NAME', 'DESCRIPTION', 'PRICE', 'BUY', 'MARGIN', 'STOCK', 'RAM', 'STORAGE', 'COLOR', 'CATEGORY', 'PUBLISHED ON'];

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

interface IForm {
    product_id: number | undefined;
    scheme_id: number | null;
    unit_price: number | null;
    products: IProductItem | null;
    enable: boolean;
}
let form = ref<IForm>({
    product_id: 0,
    scheme_id: null,
    unit_price: null,
    products: null,
    enable: false,
});

let imagePreview = ref<string | undefined>("");


const getScheme = async () => {
    form.value.scheme_id = schemeList[0].id;
}

onMounted(async () => {
    selectedSearchOption.value = searchOption.value[0];
    await getScheme();
    await getProductScheme();
    await getProduct();
});

const searchProducts = _.debounce(async () => {
    const params = {
        [selectedSearchOption.value]: searchQuery.value
    }
    const [error, data] = await productService.searchProducts(params)
    if (error) console.log(error);
    else {
        if (data.success) {
            products.value = data.data as IProductItem[]
        }
        else {
            products.value = [];
        }
    }
}, 400)

function previewImage() {
    return API_URL + imagePreview.value;
}

const getProductScheme = async () => {
    const [error, data] = await productSchemeService.getProductScheme(Number(params.value.id))
    if (error) console.log(error);
    else {
        if (data.success) {
            const productItem = data.data as IProductSchemeItem;
            form.value.product_id = productItem.products?.id;
            form.value.unit_price = productItem.unit_price;
            form.value.scheme_id = productItem.scheme_id
            form.value.scheme_id = productItem.scheme_id;
            form.value.enable = productItem.action == '1' ? true : false;
        }
    }
}

const getProduct = async (productId = form.value.product_id) => {
    if (productId) {
        const [error, data] = await productService.getProduct(productId)
        if (error) console.log(error);
        else {
            if (data.success) {
                const productItem = data.data as IProductItem;
                form.value.products = productItem;
                imagePreview.value = productItem.image;
                // form.value.products = productItem.products;
                // imagePreview.value = productItem.products?.image;
                // console.log(form.value.scheme_id)
            }
        }
    }
}


const updateProduct = async () => {
    if (form.value.unit_price === null || form.value.scheme_id === null || form.value.products === null) return Swal.fire({
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
    formData.append('product_id', form.value.products.id.toString());
    formData.append('scheme_id', form.value.scheme_id.toString());
    formData.append('unit_price', form.value.unit_price.toString());
    formData.append('action', form.value.enable ? '1' : '0');
    formData.append('_method', "PUT");

    const [error, data] = await productSchemeService.editProductScheme(Number(params.value.id), formData)
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
                router.push('/admin/show_product_scheme');
            });
        }
    }
}


const copyId = () => {
    const content = productIdCopy.value?.innerHTML;
    console.log(content)
    navigator.clipboard.writeText(content as string);
    if (tooltipCopy.value)
        tooltipCopy.value.innerHTML = "Copied"
}

const tooltipRemove = () => {
    if (tooltipCopy.value)
        tooltipCopy.value.innerHTML = "Copy to clipboard"
}

</script>

<template>
    <div class='lg:py-7 lg:px-10 p-5'>
        <div class="flex justify-between items-end">
            <h1 class='text-xl font-bold text-black_500'>Edit a product scheme</h1>
            <button class='px-4 py-3 rounded-md bg-primary text-white text-sm cursor-pointer' @click="updateProduct()">Edit
                product</button>
        </div>
        <div class="mt-8 flex flex-col gap-6 md:flex-row shadow-[#E1E1E1_0px_1px_8px] p-6 rounded-md">
            <div class='flex-[4]'>
                <div class="flex justify-between">
                    <h1 class='text-xl font-semibold text-gray-800'>Product ID</h1>
                    <div class="flex items-center gap-2">
                        <input v-model="form.enable" type="checkbox" id="enable"
                            class="accent-current border border-solid border-gray-300 rounded-sm cursor-pointer focus:ring-primary">
                        <label for="enable">Enable</label>
                    </div>
                </div>
                <div class="mt-3">
                    <input type="text" name="" id="" class='input text-sm w-full' placeholder='Write name here...'
                        v-model="form.product_id" />
                </div>


                <div class="mt-4">
                    <h1 class='text-xl font-semibold text-gray-800'>Unit Price</h1>
                    <div class='mt-2'>
                        <input type="text" name="" id="" class='input text-sm w-full' placeholder='Write name here...'
                            v-model="form.unit_price" />
                    </div>
                </div>


                <!-- <div class="border border-solid border-gray-300 rounded-lg p-4 mt-8 bg-white">
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
                </div> -->

                <div class="flex justify-between items-center mt-4">
                    <p class='text-xl font-semibold text-gray-800'>Scheme</p>
                    <!-- <RouterLink :to="'/admin/add_category'">
                            <p class='text-sm cursor-pointer font-semibold text-primary hover:underline'>Add new scheme
                            </p>
                        </RouterLink> -->
                </div>
                <select name="" id="" class="w-full mt-2 text-[14px] cursor-pointer" v-model="form.scheme_id">
                    <option v-for="item in schemeList" :value="item.id" :key="item.id">{{ item.name }}</option>
                </select>
            </div>
            <div class="flex-[7]">
                <div class="border-solid border border-gray-300 rounded-lg p-4 bg-white" v-if="form.products">
                    <div class="flex flex-row md:flex-row justify-between gap-4">
                        <div class="flex flex-row gap-4 w-full">
                            <div class='w-full'>
                                <div class="flex flex-row items-start mt-3 justify-between">
                                    <p class='mb-2 text-gray-800 text-[15px] font-semibold'>Name</p>
                                    <div class="flex items-center gap-2">
                                        <p class="text-gray-800 text-[15px] font-semibold">Product Id: <span
                                                ref="productIdCopy">{{ form.products.id }}</span>
                                        </p>
                                        <button @click="copyId()" @mouseout="tooltipRemove()" ref="tooltip"
                                            class="group relative border border-solid rounded-md p-1 text-xs bg-gray-100 hover:bg-gray-200">
                                            <span
                                                class="absolute bottom-8 right-0 bg-black text-white p-1 z-10 rounded-md hidden group-hover:block"
                                                ref="tooltipCopy">Copy to clipboard</span>
                                            Copy
                                        </button>
                                    </div>
                                </div>

                                <input type="text" placeholder='$$$' class='input w-full opacity-70' disabled
                                    v-model="form.products.name" />
                            </div>
                            <!-- <div class='w-full'>
                                <p class='mb-2 text-gray-800 text-[15px] font-semibold'>Price</p>
                                <input type="text" placeholder='$$$' class='input w-full' v-model="form.products.price" />
                            </div> -->
                        </div>
                        <div class="inline-flex gap-4" v-if="imagePreview != ''">
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
            </div>
            <div class='flex-[5]'>
                <div class="border-solid border border-gray-300 rounded-lg p-4 w-auto h-[335px] bg-white">
                    <!-- <div class="flex justify-between items-center">
                        <p class='text-xl font-semibold text-gray-800'>Scheme</p>
                        <RouterLink :to="'/admin/add_category'">
                            <p class='text-sm cursor-pointer font-semibold text-primary hover:underline'>Add new scheme
                            </p>
                        </RouterLink>
                    </div>
                    <select name="" id="" class="w-full mt-4 text-[14px] cursor-pointer" v-model="form.scheme_id">
                        <option v-for="item in schemeList" :value="item.id" :key="item.id">{{ item.name }}</option>
                    </select> -->
                    <!-- <div class="flex justify-between items-center">
                        <p class='text-xl font-semibold text-gray-800'>Scheme</p>
                    </div> -->
                    <div class="flex flex-col gap-4 overflow-hidden">
                        <div class="flex justify-between">
                            <div class='relative flex flex-row items-center'>
                                <input @keyup="searchProducts()" v-model="searchQuery" type="text" name="" id=""
                                    placeholder='Search product'
                                    class='text-sm pl-10 w-[155px] h-[40px] rounded-md border-gray-300 border-solid border focus:border-current focus:ring-current' />
                                <img :src="Upload.icon('search.svg')" alt=""
                                    class="absolute top-[50%] left-3 translate-y-[-50%] w-4 h-4" />
                            </div>
                            <div class="flex flex-row justify-between items-center gap-3">
                                <p class='text-sm font-semibold text-gray-800'>by:</p>
                                <select name="" id="" class="w-full text-[14px] cursor-pointer"
                                    v-model="selectedSearchOption">
                                    <option v-for="item in searchOption" :value="item" :key="item">{{ item }}
                                    </option>
                                </select>
                            </div>
                        </div>
                        <h1 class="text-sm font-semibold">Select a product</h1>
                        <div
                            class="w-[300px] h-[245px] border border-solid rounded-md overflow-x-auto overflow-y-none scroll-smooth scrollbar-thin scrollbar-track-gray-200 scrollbar-track-rounded-xl scrollbar-thumb-current scrollbar-thumb-rounded-xl   ">
                            <table class=''>
                                <thead class='border-solid border-b border-gray-300'>
                                    <tr>
                                        <!-- <th class='text-start text-gray-600 text-md w-[50px] py-2 px-3'>
                                        <input type="checkbox" name="" id=""
                                                class='w-4 h-4 border-solid border border-gray-500 rounded-[4px] checked:rounded-[4px]' />
                                        </th> -->
                                        <th v-for="(item, index) in header" :key="index"
                                            :class="index == 5 ? 'w-[200px] text-start' : index == 0 ? 'w-[300px] text-center' : 'text-start'"
                                            class="text-gray-600 text-xs py-2 px-3">{{ item }}</th>
                                        <th class='text-end text-gray-600 text-xs py-2 px-3 pr-4'></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr class='group relative border-solid border-b border-gray-300 bg-gray-50 cursor-pointer hover:bg-gray-200'
                                        v-for="item in products" :key="item.id" @click="getProduct(item.id)">
                                        <!-- <td className='text-sm text-gray-700 py-2 px-3'>
                                            <input type="checkbox" name="" id=""
                                                className='w-4 h-4 cursor-pointer border-solid border border-gray-500 rounded-[4px] checked:rounded-[4px]' />
                                        </td> -->
                                        <td className='py-2 px-3 flex items-center gap-1'>
                                            <img :src="Upload.image(item.image)" alt=""
                                                className='w-[45px] h-[45px] border-solid border border-gray-300 rounded-md object-cover' />
                                            <p
                                                className='w-[230px] text-ph font-semibold truncate text-gray-700  py-2 px-3 cursor-pointer'>
                                                {{ item.name }}</p>
                                        </td>
                                        <td className='text-ph font-semibold text-gray-700 py-2 px-3'>{{ item.description
                                        }}
                                        </td>
                                        <td className='text-ph font-semibold text-gray-700 py-2 px-3'>${{ item.price }}</td>
                                        <td className='text-ph font-semibold text-gray-700 py-2 px-3'>${{ item.buy }}</td>
                                        <td className='text-ph font-semibold text-gray-700 py-2 px-3'>${{ item.margin }}
                                        </td>
                                        <td className='text-ph font-semibold text-gray-700 py-2 px-3'>{{ item.stock }}</td>
                                        <td className='text-ph font-semibold text-gray-700 py-2 px-3'>{{ item.ram }}</td>
                                        <td className='text-ph font-semibold text-gray-700 py-2 px-3'>{{ item.storage }}
                                        </td>
                                        <td className='text-ph font-semibold text-gray-700 py-2 px-3'>{{ item.color }}</td>
                                        <td className='text-ph font-semibold text-gray-700 py-2 px-3'>{{ item.category?.name
                                        }}</td>
                                        <td className='text-ph font-semibold text-gray-700 py-2 px-3'>{{ item.created_at }}
                                        </td>
                                        <td class='text-lg w-4 text-gray-700 py-2 px-3 pr-5'>
                                            <div class="w-4 h-4"><img :src="Upload.icon('more.svg')" alt="" /></div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</template>