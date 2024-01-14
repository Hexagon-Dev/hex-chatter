<script setup>
import {useUserStore} from "@/stores/userStore.js";
import router from "@/plugins/router.js";

if (useUserStore().isAuthenticated) {
    router.push({ name: 'dashboard' });
} else {
    router.push({ name: 'login' });
}
</script>

<template>
    <div></div>
</template>
