import { createApp } from 'vue'
import PrimeVue from 'primevue/config';
import App from './App.vue';
import { createPinia } from 'pinia';
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate';
import router from '@/plugins/router.js';
import ToastService from 'primevue/toastservice';
import ConfirmationService from 'primevue/confirmationservice';
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome';
import Lara from '@/primevue';
import '@/plugins/icons.js';

/*if ('serviceWorker' in navigator) {
  navigator.serviceWorker.register('firebase-messaging-sw.js')
    .then(registration => {
      console.debug('Service Worker for Firebase notifications registered.');
    })
    .catch(error => {
      console.error('Service Worker registration failed:', error);
    });
}*/

const pinia = createPinia();

pinia.use(piniaPluginPersistedstate);

const app = createApp(App);

app.use(PrimeVue, {
  unstyled: true,
  pt: Lara,
  inputStyle: 'filled',
})
  .use(pinia)
  .use(router)
  .use(ToastService)
  .use(ConfirmationService)
  .component('font-awesome-icon', FontAwesomeIcon)
  .mount("#app")
