<script setup>
import {useUserStore} from "@/stores/userStore.js";
import router from "@/plugins/router.js";
import {useChatsStore} from "@/stores/chatsStore.js";
import ChatsPanel from "@/components/ChatsPanel.vue";
import {computed, ref, watch} from "vue";
import api from "@/plugins/api.js";
import {useToast} from "primevue/usetoast";
import MessageBlock from "@/components/MessageBlock.vue";
import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome";
import ContextMenu from 'primevue/contextmenu';
import {onKeyStroke} from "@vueuse/core";

const toast = useToast();

const userStore = useUserStore();
const chatsStore = useChatsStore();

if (userStore.isAuthenticated) {
  if (!userStore.user) {
    userStore.fetchMe();
  }

  if (!chatsStore.chats) {
    chatsStore.fetchChats();
  }
} else {
  router.push({ name: 'login' });
}

const currentChatIndex = ref(null);
const currentChat = computed(() => {
  if (currentChatIndex.value !== null && currentChatIndex.value === undefined) {
    return null;
  }

  if (typeof currentChatIndex.value === 'number') {
    return chatsStore.chats[currentChatIndex.value];
  }

  return currentChatIndex;
});

watch(currentChatIndex, () => {
  if (typeof currentChatIndex.value === 'number' && !chatsStore.chats[currentChatIndex.value].messages?.data?.length) {
    chatsStore.fetchMessages(chatsStore.chats[currentChatIndex.value].id, toast);
    chatsStore.listenChat(chatsStore.chats[currentChatIndex.value].id);
  }
});

const isLoading = ref(false);

async function createChat() {
  isLoading.value = true;

  const response = await api.post('chats', currentChatIndex.value);

  if (response.status === 201) {
    const chat = { ...response.data.chat, messages: [] };

    chatsStore.chats.push(chat);
    currentChatIndex.value = chat.id;
  } else {
    toast.add({ severity:'error', summary: 'Error', detail: response.data.message ?? 'Something went wrong' });
  }
}

const message = ref('');

async function sendMessage() {
  const content = message.value.trim();

  if (!content) {
    return;
  }

  message.value = '';

  if (typeof currentChatIndex.value !== 'number') {
    await createChat();
  }

  const tempId = currentChat.value.messages.data.length + 1;

  if (!currentChat.value.messages.data) {
    currentChat.value.messages.data = [];
  }

  currentChat.value.messages.data.push({
    id: tempId,
    content,
    user_id: userStore.user.id,
    created_at: new Date(),
    loading: true,
  });

  const response = await api.post(`chats/${currentChat.value.id}/messages`, { content });

  if (response.status === 201) {
    const index = currentChat.value.messages.data.findIndex(m => m.id === tempId);

    currentChat.value.messages.data[index] = response.data;

    currentChat.value.last_message = response.data;
  } else {
    toast.add({ severity:'error', summary: 'Error', detail: response.data.message ?? 'Something went wrong' });
  }
}

async function editMessage() {
  const content = message.value.trim();

  if (!content) {
    return;
  }

  message.value = '';

  const index = currentChat.value.messages.data.findIndex(m => m.id === editMessageId.value);

  if (index === -1) {
    return;
  }

  const id = editMessageId.value;

  currentChat.value.messages.data[index].content = content;
  currentChat.value.messages.data[index].loading = true;
  editMessageId.value = null;

  const response = await api.put(`chats/${currentChat.value.id}/messages/${id}`, { content });

  if (response.status === 200) {
    currentChat.value.messages.data[index] = response.data;
  } else {
    toast.add({ severity:'error', summary: 'Error', detail: response.data.message ?? 'Something went wrong' });
  }
}

async function deleteMessage(messageId) {
  const index = currentChat.value.messages.data.findIndex(m => m.id === messageId);

  if (index === -1) {
    return;
  }

  const response = await api.delete(`chats/${currentChat.value.id}/messages/${messageId}`);

  if (response.status === 204) {
    currentChat.value.messages.data.splice(index, 1);
  } else {
    toast.add({ severity:'error', summary: 'Error', detail: response.data.message ?? 'Something went wrong' });
  }
}

const contextMenu = ref(null);
const contextMenuMessageId = ref(null);
const contextMenuItems = ref([
  {
    label: 'Copy text',
    icon: 'fa-solid fa-copy',
    command: () => {
      const selectedMessage = currentChat.value.messages.data.find(m => m.id === contextMenuMessageId.value);

      if (selectedMessage) {
        navigator.clipboard.writeText(selectedMessage.content);
      }
    }
  },
  {
    label: 'Delete',
    icon: 'fa-solid fa-trash',
    command: () => {
      deleteMessage(contextMenuMessageId.value);
    }
  },
  {
    label: 'Edit',
    icon: 'fa-solid fa-pencil',
    command: () => {
      const selectedMessage = currentChat.value.messages.data.find(m => m.id === contextMenuMessageId.value);

      if (selectedMessage) {
        editMessageId.value = selectedMessage.id;
        message.value = selectedMessage.content.trim();
      }
    }
  }
]);

function showContextMenu(event, messageId) {
  contextMenuMessageId.value = messageId;
  contextMenu.value.show(event);
}

onKeyStroke('Escape', (e) => {
  e.preventDefault();

  if (editMessageId.value) {
    editMessageId.value = null;
    message.value = '';
  } else if (!contextMenu.value.visible && currentChatIndex.value !== null) {
    currentChatIndex.value = null;
  }
});

onKeyStroke('ArrowUp', (e) => {
  e.preventDefault();

  if (!editMessageId.value && currentChat.value.messages?.data?.length) {
    const lastOwnMessage = currentChat.value.messages.data
      .slice()
      .reverse()
      .find(m => m.user_id === userStore.user.id);

    if (lastOwnMessage) {
      editMessageId.value = lastOwnMessage.id;
      message.value = lastOwnMessage.content;
    }
  }
});

const editMessageId = ref(null);

onKeyStroke('Enter', (e) => {
  e.preventDefault();

  if (editMessageId.value) {
    editMessage();
  } else {
    sendMessage();
  }
});
</script>

<template>
  <div class="h-full w-full flex overflow-hidden">
    <ChatsPanel v-model="currentChatIndex" />

    <div class="w-full">
      <div v-if="!currentChat.users" class="h-full w-full flex items-center justify-center font-bold select-none">
        Select chat to start messaging
      </div>

      <div v-else class="flex flex-col h-full">
        <div class="bg-surface-800 w-full h-16 p-2 flex-none flex items-center">
          <p class="font-bold">{{ currentChat.users.find(u => u.id !== userStore.user.id).name }}</p>
        </div>

        <div class="h-full w-full overflow-y-auto bg-surface-900">
          <div v-if="!currentChat?.messages?.data?.length" class="h-full w-full flex items-center justify-center">
            No messages yet
          </div>

          <div v-else class="h-full flex flex-col justify-end gap-1 p-2">
            <MessageBlock
              v-for="(message, index) in currentChat.messages.data"
              :key="message.id"
              :message="message"
              :class="[{
                'mt-1': currentChat.messages.data[index - 1] && currentChat.messages.data[index - 1].user_id !== message.user_id,
                'mb-1': currentChat.messages.data[index + 1] && currentChat.messages.data[index + 1].user_id !== message.user_id,
              }]"
              @contextmenu="showContextMenu($event, message.id)"
            />
          </div>
        </div>

        <div v-if="editMessageId" class="bg-surface-800 w-full flex items-center gap-2 flex-none px-3">
          <font-awesome-icon icon="fa-solid fa-pencil" class="text-xl" />

          <div class="flex flex-col gap-0.5 p-1 text-sm">
            <p class="text-xs font-bold">Editing message</p>
            <p>{{ currentChat.messages.data.find(m => m.id === editMessageId).content }}</p>
          </div>
        </div>

        <div class="bg-surface-800 w-full flex items-center flex-none">
          <InputText
            placeholder="Write message..."
            class="w-full !border-none focus:!outline-none focus:!ring-0 !bg-surface-800"
            v-model="message"
          />
        </div>
      </div>
    </div>

    <ContextMenu ref="contextMenu" :model="contextMenuItems" @hide="contextMenuMessageId = null">
      <template #item="{ item, props }">
        <a class="flex align-items-center" v-bind="props.action">
          <font-awesome-icon :icon="item.icon" class="mr-2" />
          <span class="ml-2">{{ item.label }}</span>
        </a>
      </template>
    </ContextMenu>
  </div>
</template>
