import { defineStore } from 'pinia';
import api from "@/plugins/api.js";
import {echo} from "@/plugins/socket.js";

// eslint-disable-next-line import/prefer-default-export
export const useChatsStore = defineStore('chatsStore', {
  state: () => ({
    chats: null
  }),
  actions: {
    async fetchChats() {
      const response = await api.get('chats');

      this.chats = response.data.chats;
    },
    async fetchMessages(chatId, toast) {
      const response = await api.get(`chats/${chatId}/messages`);

      if (response.status === 200) {
        this.chats[this.chats.findIndex(c => c.id === chatId)].messages = response.data;
      } else {
        toast?.add({severity: 'error', summary: 'Error', detail: 'Something went wrong', life: 3000});
      }
    },
    listenChat(chatId) {
      if (echo?.private(`chat.${chatId}`)) {
        return;
      }

      echo.private(`chat.${chatId}`)
        .listen('.message.created', (e) => {
          this.chats[this.chats.findIndex(c => c.id === chatId)].messages.push(e.message);
        })
        .listen('.message.updated', (e) => {
          this.chats[this.chats.findIndex(c => c.id === chatId)].messages[this.chats[this.chats.findIndex(c => c.id === chatId)].messages.findIndex(m => m.id === e.message.id)] = e.message;
        })
        .listen('.message.deleted', (e) => {
          this.chats[this.chats.findIndex(c => c.id === chatId)].messages.splice(this.chats[this.chats.findIndex(c => c.id === chatId)].messages.findIndex(m => m.id === e.message.id), 1);
        });
    }
  },
});
