<script setup lang="ts">
import Swal from 'sweetalert2';
import { onMounted, ref, computed } from 'vue';
import { RouteParams, useRoute, useRouter } from 'vue-router';
import { ICategoryItem } from '../types/Category';
import { categoryService } from '../service/api/modules/category.api';
import { IFormCategory } from '../types/Form';

const router = useRouter();
const route = useRoute();
const params = computed<RouteParams>(() => route.params);

let validateMessage = ref({ image: "", });
let loading = ref<boolean>(false);
let imagePreview = ref<string>('');

let form = ref<IFormCategory>({
    name: '',
    description: '',
    image: null,
    enable: false,
});

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

    loading.value = true;

    const formData = new FormData();
    formData.append('name', form.value.name);
    formData.append('des', form.value.description);
    formData.append('action', form.value.enable ? '1' : '0');
    formData.append('_method', 'PUT');
    if (form.value.image) {
        formData.append('image', form.value.image);
    }

    const [error, data] = await categoryService.editCategory(Number(params.value.id), formData)
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
            router.push('/admin/show_category');
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
        if (allowExtenstions.exec(target.value)) {
            form.value.image = file;
            validateMessage.value.image = ""
        }
        else {
            validateMessage.value.image = "Image should be jpg, jpeg, png, gif";
        }
    }
}

</script>

<template>
    <div>
        <div class="d-flex flex-row justify-space-between align-center flex-wrap mb-4">
            <h1 class='text-header font-weight-medium'>Edit a category</h1>
            <v-btn @click="updateCategory()" color="success" :loading="loading" flat>
                Edit category
            </v-btn>
        </div>
        <v-layout>
            <v-row>
                <v-col>
                    <div class="d-flex justify-space-between align-center">
                        <div class="font-weight-medium text-grey-darken-4">Name</div>
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
                        <v-textarea v-model="form.description" density="compact" placeholder="Write tag here..."
                            variant="outlined" hide-details></v-textarea>
                    </div>
                    <div class="mt-7">
                        <div class="mb-2 font-weight-medium text-grey-darken-4">Display images</div>
                        <span class="text-red-darken-1">{{ validateMessage.image }}</span>
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
            </v-row>
        </v-layout>
    </div>
</template>