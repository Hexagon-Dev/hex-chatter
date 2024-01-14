import io from 'socket.io-client';
import Echo from 'laravel-echo';
import {useUserStore} from "@/stores/userStore.js";

const userStore = useUserStore();

window.io = io;

export const echo = new Echo({
  broadcaster: 'socket.io',
  host: window.location.hostname + ':' + (import.meta.env.VITE_ECHO_PORT ?? 6001),
  bearerToken: userStore.token,
});
