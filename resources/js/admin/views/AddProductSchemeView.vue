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
    return imagePreview.value;
}
</script>

<template>
    <div>
        <div class="d-flex flex-row justify-space-between align-center flex-wrap mb-4">
            <h1 class='text-header font-weight-medium'>Add a product scheme</h1>
            <v-btn @click="saveProduct()" color="success" flat>
                publish
            </v-btn>
        </div>
        <v-layout>
            <v-row>
                <v-col cols="12" md="8">
                    <div class="mt-7">
                        <div class="mb-2 font-weight-medium text-grey-darken-4">Unit Price</div>
                        <v-text-field v-model="form.unit_price" density="compact" placeholder="Write description here..."
                            variant="outlined" hide-details></v-text-field>
                    </div>
                    <div class="mt-7 p-3 border border-solid rounded-md" v-if="form.products">
                        <div class="d-flex flex-column flex-md-row justify-space-between">
                            <div class="d-flex flex-row justify-space-between w-100 mr-3">
                                <v-responsive class="mr-3">
                                    <div class="mb-2 font-weight-medium text-grey-darken-4">Name</div>
                                    <v-text-field v-model="form.products.name" density="compact" placeholder="Write here..."
                                        variant="outlined" hide-details disabled></v-text-field>
                                </v-responsive>
                                <div>
                                    <div class="gap-4 mt-1" v-if="imagePreview != ''">
                                        <v-img :src="previewImage()" aspect-ratio="1/1" :width="100" alt="" cover></v-img>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-3 d-flex flex-column flex-md-row justify-space-between">
                            <div class="w-100 mr-3">
                                <div class="mb-2 font-weight-medium text-grey-darken-4">Price</div>
                                <v-text-field v-model="form.products.price" density="compact"
                                    placeholder="Write ram here..." variant="outlined" hide-details disabled></v-text-field>
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
                                <v-text-field v-model="form.products.ram" density="compact" placeholder="Write ram here..."
                                    variant="outlined" hide-details disabled></v-text-field>
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
                <v-col cols="12" md="4">
                    <div class="border-solid border border-gray-300 rounded-lg p-4 h-[305px] bg-white mt-2 md:mt-10">
                        <div class="d-flex flex-wrap justify-space-between align-center">
                            <div class='font-weight-medium text-grey-darken-4'>Product Scheme</div>
                            <!-- <RouterLink :to="'/admin/add_category'">
                                <v-btn color="blue" class='text-none' variant="tonal" flat>Add
                                    category
                                </v-btn>
                            </RouterLink> -->
                        </div>
                        <select name="" id="" class="w-full mt-4 text-[14px] cursor-pointer" v-model="form.scheme_id">
                            <option v-for="item in schemeList" :value="item.id" :key="item.id">{{ item.name }}</option>
                        </select>
                    </div>
                </v-col>
            </v-row>
        </v-layout>
    </div>
</template>