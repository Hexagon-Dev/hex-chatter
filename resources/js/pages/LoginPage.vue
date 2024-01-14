<script setup>
import {ref} from 'vue';
import api from "@/plugins/api.js";
import router from "@/plugins/router.js";
import {useToast} from "primevue/usetoast";
import {useUserStore} from "@/stores/userStore.js";

const userStore = useUserStore();
const toast = useToast();
const form = ref({
    email: '',
    password: '',
});
const isLoading = ref(false);
const formEl = ref();

async function submitLogin() {
    if (!formEl.value.reportValidity()) {
        return;
    }

    isLoading.value = true;

    const response = await api.post('/login', form.value);

    if (response.status === 200) {
        userStore.token = response.data.access_token;

        if (userStore.token && !userStore.user) {
            await userStore.fetchMe();
        }

        await router.push({ name: 'dashboard' });
    } else {
        toast.add({
            severity: 'error',
            summary: 'Error Message',
            detail: 'Invalid credentials',
            life: 3000
        });
    }

    isLoading.value = false;
}
</script>

<template>
    <div class="flex items-center justify-center h-full">
        <form class="flex flex-col gap-4" ref="formEl">
            <div>
                <label for="email" class="block">Email</label>
                <InputText type="email" id="email" class="w-64" v-model="form.email" required />
            </div>

            <div>
                <label for="password" class="block">Password</label>
                <InputText type="password" id="password" class="w-64" v-model="form.password" required @keydown.enter="submitLogin" />
            </div>

            <Button @click="submitLogin" label="Login" />
        </form>
    </div>
</template>
