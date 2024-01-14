<script setup>
import {RouterView} from 'vue-router';
import {useUserStore} from "@/stores/userStore.js";
import ConfirmDialog from "primevue/confirmdialog";
import Toast from "primevue/toast";
// import { firebaseWrapper } from '@/plugins/firebase.js';

const userStore = useUserStore();

/*if (userStore.user) {
  firebaseWrapper.firebaseInit();
}*/

if (userStore.token && !userStore.user) {
  userStore.fetchMe();
}
</script>

<template>
  <RouterView v-slot="{ Component }">
    <transition mode="out-in">
      <div
        v-if="userStore.isLoading"
        class="h-full w-full flex flex-col justify-center items-center gap-4"
        key="loading"
      >
        <font-awesome-icon icon="fa-solid fa-spinner" spin class="text-4xl" />
        <p class="text-xl font-bold">Loading user data...</p>
      </div>

      <div class="h-full" v-else key="content">
        <ConfirmDialog />
        <Toast />
        <transition mode="out-in">
          <component :is="Component" />
        </transition>
      </div>
    </transition>
  </RouterView>
</template>
