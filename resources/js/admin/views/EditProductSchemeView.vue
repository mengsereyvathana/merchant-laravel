<script setup lang="ts">
import Swal from 'sweetalert2';
import SearchComponent from '@/admin/components/SearchComponent.vue';
import _ from "lodash";
import { computed } from '@vue/reactivity';
import { onMounted, ref } from 'vue';
import { productService } from '../service/api/modules/product.api'
import { RouteParams, useRoute, useRouter } from 'vue-router';
import { IProductItem } from '../types/Product';
import { IProductSchemeItem } from '../types/ProductScheme';
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
let searchOption = ref([
    { id: 1, title: "Name", by: "name" },
    { id: 2, title: "ID", by: "id" },
]);

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
    selectedSearchOption.value = searchOption.value[0].by;
    await getScheme();
    await getProductScheme();
    await getProduct();
});

const currentSearchUpdated = async (value: string, selectOptions: string) => {
    selectedSearchOption.value = selectOptions;
    searchQuery.value = value;
    await searchProducts();
}

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
    return imagePreview.value;
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
    <div>
        <div class="d-flex flex-row justify-space-between align-center flex-wrap mb-4">
            <h1 class='text-header font-weight-medium'>Edit a product scheme</h1>
            <v-btn @click="updateProduct()" color="success" flat>
                Edit Product
            </v-btn>
        </div>
        <v-layout>
            <v-responsive>
                <v-row>
                    <v-col cols="12" md="3">
                        <div class="d-flex justify-space-between align-center">
                            <div class="font-weight-medium text-grey-darken-4">Product Id</div>
                            <div class="flex items-center gap-2">
                                <v-checkbox v-model="form.enable" label="enable" density="compact" color="success"
                                    hide-details></v-checkbox>
                            </div>
                        </div>
                        <div>
                            <v-text-field v-model="form.product_id" density="compact" placeholder="Write id here..."
                                variant="outlined" hide-details></v-text-field>
                        </div>
                        <div class="mt-7">
                            <div class="mb-2 font-weight-medium text-grey-darken-4">Unit Price</div>
                            <v-text-field v-model="form.unit_price" density="compact"
                                placeholder="Write description here..." variant="outlined" hide-details></v-text-field>
                        </div>
                        <div class="mt-7">
                            <div class="mb-2 font-weight-medium text-grey-darken-4">Scheme</div>
                            <select name="" id="" class="w-full text-[14px] cursor-pointer" v-model="form.scheme_id">
                                <option v-for="item in schemeList" :value="item.id" :key="item.id">{{ item.name }}</option>
                            </select>
                        </div>
                    </v-col>
                    <v-col cols="12" md="4">
                        <div class="p-3 border border-solid rounded-md" v-if="form.products">
                            <div class="d-flex flex-column flex-md-row justify-space-between">
                                <v-row>
                                    <v-col cols="8">
                                        <div class="d-flex justify-space-between align-center">
                                            <div class="mb-2 font-weight-medium text-grey-darken-4">Name</div>
                                            <div class="mb-2 d-flex align-center">
                                                <div class="mr-2 font-weight-medium text-grey-darken-4"> Product Id: <span
                                                        ref="productIdCopy">{{ form.products.id }}</span> </div>
                                                <button @click="copyId()" @mouseout="tooltipRemove()" ref="tooltip"
                                                    class="group relative border border-solid rounded-md p-1 text-xs bg-gray-100 hover:bg-gray-200">
                                                    <span
                                                        class="absolute bottom-6 right-0 bg-black text-white p-1 z-10 rounded-md hidden group-hover:block"
                                                        ref="tooltipCopy">Copy to clipboard</span>
                                                    Copy
                                                </button>
                                            </div>
                                        </div>
                                        <v-text-field v-model="form.products.name" density="compact"
                                            placeholder="Write here..." variant="outlined" hide-details disabled
                                            single-line></v-text-field>
                                    </v-col>

                                    <v-col cols="4">
                                        <div class="mt-1" v-if="imagePreview != ''">
                                            <v-img :src="previewImage()" aspect-ratio="1/1" :width="100" :height="100"
                                                alt="" cover class="rounded-md"></v-img>
                                        </div>
                                    </v-col>
                                </v-row>
                            </div>
                            <div class="mt-3 d-flex flex-column flex-md-row justify-space-between">
                                <div class="w-100 mr-3">
                                    <div class="mb-2 font-weight-medium text-grey-darken-4">Price</div>
                                    <v-text-field v-model="form.products.price" density="compact"
                                        placeholder="Write ram here..." variant="outlined" hide-details
                                        disabled></v-text-field>
                                </div>
                                <div class="w-100 mt-3 mt-md-0">
                                    <div class="mb-2 font-weight-medium text-grey-darken-4">Stock</div>
                                    <v-text-field v-model="form.products.stock" density="compact"
                                        placeholder="Write storage here..." variant="outlined" hide-details
                                        disabled></v-text-field>
                                </div>
                            </div>
                            <div class="mt-3 d-flex flex-column flex-md-row justify-space-between">
                                <div class="w-100 mr-3">
                                    <div class="mb-2 font-weight-medium text-grey-darken-4">Ram</div>
                                    <v-text-field v-model="form.products.ram" density="compact"
                                        placeholder="Write ram here..." variant="outlined" hide-details
                                        disabled></v-text-field>
                                </div>
                                <div class="w-100 mt-3 mt-md-0">
                                    <div class="mb-2 font-weight-medium text-grey-darken-4">Stock</div>
                                    <v-text-field v-model="form.products.storage" density="compact"
                                        placeholder="Write storage here..." variant="outlined" hide-details
                                        disabled></v-text-field>
                                </div>
                            </div>
                        </div>
                    </v-col>
                    <v-col cols="12" md="5">
                        <div class="border-solid border border-gray-300 rounded-lg p-4 w-auto h-[390px] bg-white">
                            <div class="flex flex-col gap-4 overflow-hidden">
                                <v-row>
                                    <v-col>
                                        <SearchComponent :search-option="searchOption"
                                            @current-search-updated="currentSearchUpdated" />
                                    </v-col>
                                </v-row>
                                <v-row>
                                    <v-col>
                                        <h1 class="text-sm font-semibold">Select a product</h1>
                                        <div
                                            class="h-[245px] border border-solid rounded-md overflow-x-auto overflow-y-none scroll-smooth">
                                            <v-table>
                                                <thead>
                                                    <tr>
                                                        <th v-for="(item, index) in header" :key="index"
                                                            :class="index == 5 ? 'w-[200px] text-start' : index == 0 ? 'w-[300px] text-center' : 'text-start'"
                                                            class="text-grey-darken-2 py-2 px-3">{{ item }}</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <tr class='group relative cursor-pointer' v-for="item in products"
                                                        :key="item.id" @click="getProduct(item.id)">
                                                        <td>
                                                            <div class="d-flex flex-row align-center">
                                                                <v-img :src="Upload.image(item.image)" alt=""
                                                                    aspect-ratio="1/1" class='rounded-md mr-3' cover
                                                                    :max-width="50" :width="50"></v-img>
                                                                <v-hover>
                                                                    <template v-slot:default="{ isHovering, props }">
                                                                        <span v-bind="props" class="text-grey-darken-3"
                                                                            :class="isHovering ? 'text-grey-darken-4' : ''">{{
                                                                                item.name
                                                                            }}</span>
                                                                    </template>
                                                                </v-hover>
                                                            </div>
                                                        </td>
                                                        <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{
                                                            item.description }}
                                                        </td>
                                                        <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{ item.price
                                                        }}</td>
                                                        <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{ item.buy }}
                                                        </td>
                                                        <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{ item.margin
                                                        }}</td>
                                                        <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{ item.stock
                                                        }}</td>
                                                        <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{ item.ram }}
                                                        </td>
                                                        <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{ item.storage
                                                        }}</td>
                                                        <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{ item.color
                                                        }}</td>
                                                        <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{
                                                            item.category?.name
                                                        }}
                                                        </td>
                                                        <td class='py-2 px-3 text-body-2 text-grey-darken-3'>{{
                                                            item.created_at }}
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </v-table>
                                        </div>
                                    </v-col>
                                </v-row>
                            </div>
                        </div>
                    </v-col>
                </v-row>
            </v-responsive>
        </v-layout>
    </div>
</template>


