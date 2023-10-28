<script setup lang="ts">
import { ref, onMounted } from 'vue';
import PopupComponent from '@/admin/components/PopupComponent.vue';
import { Upload } from '../../service/helpers/index'
import { adminAuthService } from '../../service/api/modules/auth-admin.api';
import router from '@/router';
import { IUserDataItem } from '../../types/IUserData';
import { toggleMenu, removeRail } from '@/admin/store/toggle';

let user = ref<IUserDataItem>();
let loading = ref<boolean>(false);

onMounted(async () => {
    await getUser();
});

const getUser = async () => {
    const [error, data] = await adminAuthService.getUser();
    if (error) console.log(error)
    else {
        if (data.success) {
            user.value = data.data;
        }
    }
}

const isConfirmedUpdated = (value: boolean) => {
    if (value) {
        logout();
    }
}

const logout = async () => {
    loading.value = true;
    const [error, data] = await adminAuthService.logout();
    if (error) console.log(error)
    else {
        if (data.success) {
            router.push("/admin/login")
        }
        loading.value = false;
    }
}

const yourProfile = () => {
    router.push("/admin/show_profile")
}
</script>

<template>
    <div class="border border-solid rounded-md mb-3">
        <v-layout class="d-flex align-center p-2">
            <v-app-bar-nav-icon class="block xl:hidden" variant="text"
                @click.stop="toggleMenu = !toggleMenu; removeRail = false;"></v-app-bar-nav-icon>

            <!-- <v-toolbar-title>My files</v-toolbar-title> -->

            <v-spacer></v-spacer>

            <v-menu rounded size="x-small">
                <template v-slot:activator="{ props }">
                    <v-btn icon v-bind="props" size="small" variant="tonal" flat>
                        <v-avatar color="blue">
                            <v-img :src="Upload.image(user?.image)" :alt="user?.name"></v-img>
                        </v-avatar>
                    </v-btn>
                </template>
                <v-card>
                    <v-card-text>
                        <div class="mx-auto text-center">
                            <v-avatar>
                                <v-img :src="Upload.image(user?.image)" :alt="user?.name"></v-img>
                            </v-avatar>
                            <h3>{{ user?.name }}</h3>
                            <p class="text-caption mt-1">
                                {{ user?.email }}
                            </p>
                            <p>Role: {{ user?.role === 3 ? "admin" : "user" }}</p>
                            <v-divider class="my-3"></v-divider>
                            <v-btn @click="yourProfile()" rounded variant="text">
                                Edit Account
                            </v-btn>
                            <v-divider class="my-3"></v-divider>
                            <!-- <v-btn @click="logout()" rounded variant="text">
                                Logout
                            </v-btn> -->
                            <PopupComponent title="Logout" :icon="false" text="Do you want to logout?" :loading="loading"
                                button-dialog="Logout" confirm-button-text="Logout" @is-confirmed="isConfirmedUpdated" />
                        </div>
                    </v-card-text>
                </v-card>
            </v-menu>
        </v-layout>
    </div>
</template>