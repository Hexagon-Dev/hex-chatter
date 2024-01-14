import { defineStore } from 'pinia';
import api from "@/plugins/api.js";
import router from "@/plugins/router.js";

// eslint-disable-next-line import/prefer-default-export
export const useUserStore = defineStore('userStore', {
  state: () => ({
    token: null,
    user: null,
    isLoading: false,
  }),
  getters: {
    isAuthenticated: (state) => state.token !== null,
  },
  actions: {
    async fetchMe() {
      this.isLoading = true;

      const response = await api.get('users/me');

      this.user = response.data;

      this.isLoading = false;
    },
    logout() {
      this.token = null;
      this.user = null;

      router.push('/');
    },
  },
  persist: {
    storage: localStorage,
    key: 'application',
  },
});
