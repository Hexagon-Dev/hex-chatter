<script setup>
import {useUserStore} from "@/stores/userStore.js";
import {format} from "date-fns";
import LoadingSpinner from "@/components/LoadingSpinner.vue";

const userStore = useUserStore();

defineProps({
  message: {
    type: Object,
    required: true,
  },
});
</script>

<template>
  <div
    class="message-block"
    :class="[message.user_id === userStore.user.id ? 'bg-surface-600' : 'bg-surface-800']"
  >
    <div class="content">{{ message.content }}</div>
    <div class="time">{{ format(message.created_at, 'HH:mm') }}</div>
    <div v-if="message.loading" class="loading">
      <LoadingSpinner />
    </div>
  </div>
</template>
