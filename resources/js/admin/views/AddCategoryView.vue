<script setup lang="ts">
import Swal from 'sweetalert2';
import { onMounted, ref } from 'vue';
import { useRouter } from 'vue-router';
import { categoryService } from '../service/api/modules/category.api'

const router = useRouter();

onMounted(() => {
    // sessionStorage.setItem('access_token', 'laravel_sanctum_wxMkVJgkLWQe9ttkxeODbGi6SlL28XDyGEbhyWtJ02075735')
    // token.value = sessionStorage.getItem('access_token') || '';
});

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

const previewImage = () => form.value.image ? URL.createObjectURL(form.value.image) : "";

const browseImage = (e: Event) => {
    const target = e.target as HTMLInputElement;
    const files: FileList | null = target.files;
    if (files && files.length > 0) {
        const file = files[0];
        const allowExtenstions = /(\.jpg|\.jpeg|\.png|\.gif)$/i;
        if (allowExtenstions.exec(target.value)) form.value.image = file;
    }
}

const saveCategory = async () => {
    if (form.value.name == "" || form.value.description == "") return Swal.fire({
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

    if (!form.value.image) return Swal.fire({
        toast: true,
        position: 'top',
        showClass: {
            icon: 'animated heartBeat delay-1s'
        },
        icon: 'error',
        text: 'Please choose one image',
        showConfirmButton: false,
        timer: 1000
    });

    const formData = new FormData();
    formData.append('name', form.value.name);
    formData.append('des', form.value.description);
    formData.append('image', form.value.image);
    formData.append('action', form.value.enable ? '1' : '0');

    const [error, data] = await categoryService.createCategory(formData)
    if (error) console.log(error)
    else {
        Swal.fire({
            toast: true,
            position: 'top',
            showClass: {
                icon: 'animated heartBeat delay-1s'
            },
            icon: 'success',
            text: 'Category has been saved',
            showConfirmButton: false,
            timer: 1000
        }).then(() => {
            router.push("/admin/show_category");
        })
    }
}
const removeImage = () => {
    if (form.value.image) {
        form.value.image = null;
    }
}
</script>

<template>
    <div>
        <div class="d-flex flex-row justify-space-between align-center flex-wrap mb-4">
            <h1 class='text-header font-weight-medium'>Add a category</h1>
            <v-btn @click="saveCategory()" color="success" flat>
                Publish
            </v-btn>
        </div>
        <v-layout>
            <v-row>
                <v-col>
                    <div class="d-flex justify-space-between align-center">
                        <div class="font-weight-medium text-grey-darken-4">Category name</div>
                        <div class="flex items-center gap-2">
                            <v-checkbox v-model="form.enable" label="enable" color="success" density="compact"
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
                    <div class="mt-7">
                        <div class="mb-2 font-weight-medium text-grey-darken-4">Display images</div>

                        <div class="mt-1" v-if="form.image">
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
            </v-row>
        </v-layout>
    </div>
</template>