<script setup>
import ResizablePanel from "@/components/ResizablePanel.vue";
import {useChatsStore} from "@/stores/chatsStore.js";
import {useDebounceFn, useWindowSize} from "@vueuse/core";
import {computed, ref, watch} from "vue";
import api from "@/plugins/api.js";
import {useToast} from "primevue/usetoast";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import {useUserStore} from "@/stores/userStore.js";
import { format } from 'date-fns';

const toast = useToast();
const chatsStore = useChatsStore();
const userStore = useUserStore();

const { width, height } = useWindowSize();

const search = ref('');

const chats = computed(() => {
  if (!search.value) {
    return chatsStore.chats;
  }

  return chatsStore.chats?.filter(chat => chat.name.toLowerCase().includes(search.value.toLowerCase()));
});

watch(search, () => {
  if (!chats.value?.length) {
    isLoading.value = true;

    useDebounceFn(() => {
      fetchUsers();
    }, 1000)();
  }
});

const isLoading = ref(false);
const users = ref([]);

async function fetchUsers() {
  isLoading.value = true;

  const response = await api.get(`users/${search.value}`);

  if (response.status === 200) {
    users.value = response.data;
  } else {
    toast.add({ severity:'error', summary: 'Error', detail: 'Something went wrong', life: 3000 });
  }

  isLoading.value = false;
}

const model = defineModel();

function createChat(user) {
  model.value = {
    id: null,
    type: 0,
    users: [user, userStore.user],
  };
}

async function selectChat(chat) {
  model.value = chatsStore.chats.findIndex(c => c.id === chat.id);
}

function chatName(chat) {
  if (chat.type === 0) {
    return chat.users.find(u => u.id !== userStore.user.id).name;
  }
}
</script>

<template>
  <ResizablePanel
    :initial-width="300"
    :min-width="300"
    :max-width="width - 300"
    class="flex-none"
  >
    <div class="p-2 flex gap-2 w-full">
      <Button text class="flex-none">
        <template #icon>
          <font-awesome-icon icon="fa-solid fa-bars" />
        </template>
      </Button>

      <div class="w-full relative">
        <InputText placeholder="Search" class="w-full pr-16" v-model="search" />

        <div class="h-full absolute top-0 right-0 flex items-center">
          <Button text v-if="search" @click="search = null" class="!w-10">
            <template #icon>
              <font-awesome-icon icon="fa-solid fa-times" />
            </template>
          </Button>
        </div>
      </div>
    </div>

    <div
      v-if="!chatsStore.chats?.length && !search"
      class="h-full w-full flex items-center justify-center font-bold select-none"
    >
      No chats yet
    </div>

    <div v-else>
      <div v-if="isLoading" class="h-16 w-full flex items-center justify-center">
        <font-awesome-icon icon="fa-solid fa-spinner" spin class="text-2xl" />
      </div>

      <template v-else>
        <div
          v-for="user in users"
          :key="user.id"
          class="dark:bg-surface-800 dark:hover:bg-surface-700 bg-surface-100 duration-200 h-16 p-2 flex items-center gap-2 cursor-pointer"
          @click="createChat(user)"
        >
          <div class="size-12 rounded-full bg-surface-500"></div>

          <p>{{ user.name }}</p>
        </div>
      </template>

      <div v-if="isLoading || users?.length" class="w-full h-2 dark:bg-surface-800 bg-surface-100" />

      <div
        v-for="(chat, index) in chatsStore.chats"
        :key="chat.id"
        class="dark:bg-surface-800 dark:hover:bg-surface-700 bg-surface-100 duration-200 h-16 p-2 flex items-center gap-2 cursor-pointer"
        :class="[{ 'dark:!bg-surface-700': model === index }]"
        @click="selectChat(chat)"
      >
        <div class="size-12 rounded-full bg-surface-500 flex-none"></div>

        <div class="flex flex-col w-full">
          <div class="flex justify-between">
            <span class="truncate">{{ chatName(chat) }}</span>
            <span v-if="chat.last_message">{{ format(chat.last_message.created_at, 'HH:mm') }}</span>
          </div>
          <div v-if="chat.last_message">
            {{ chat.last_message.user_id === userStore.user.id ? 'You: ' : '' }}
            {{ chat.last_message.content }}
          </div>
        </div>
      </div>
    </div>
  </ResizablePanel>
</template>
