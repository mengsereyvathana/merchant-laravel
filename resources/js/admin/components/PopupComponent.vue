<script setup lang="ts">
import { computed } from 'vue';
import { ref } from 'vue';

const dialog = ref<boolean>(false);

const emits = defineEmits(['isConfirmed']);

const props = defineProps<{
    id?: number | undefined,
    title: string,
    text: string,
    buttonDialog?: string,
    icon: boolean | string,
    loading: boolean,
    cancelButtonText?: string | undefined,
    confirmButtonText?: string | undefined,
}>();

const id = computed<number | undefined>(() => props.id);
const title = computed<string>(() => props.title);
const text = computed<string>(() => props.text);
const buttonDialog = computed<string | undefined>(() => props.buttonDialog);
const cancelButtonText = computed<string | undefined>(() => props.cancelButtonText);
const confirmButtonText = computed<string | undefined>(() => props.confirmButtonText);
const icon = computed<string | boolean>(() => props.icon);
const loading = computed<boolean>(() => props.loading);

const confirm = () => {
    dialog.value = false;
    emits("isConfirmed", true, id.value)
}

</script>
<template>
    <v-dialog transition="dialog-bottom-transition" v-model="dialog" persistent max-width="500">
        <template v-slot:activator="{ props }">
            <v-btn :loading="loading" size="small" :icon="icon" color="red" :text="buttonDialog" v-bind="props"
                flat></v-btn>
        </template>
        <v-card>
            <v-card-title class="text-h5">
                {{ title }}
            </v-card-title>
            <v-card-text>{{ text }}</v-card-text>
            <v-divider></v-divider>
            <v-card-actions>
                <v-spacer></v-spacer>
                <v-btn color="green" @click="dialog = false">
                    {{ cancelButtonText === undefined ? 'Cancel' : cancelButtonText }}
                </v-btn>
                <v-btn color="red" @click="confirm()">
                    {{ confirmButtonText === undefined ? 'Confirm' : confirmButtonText }}
                </v-btn>
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>