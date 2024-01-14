import { initializeApp } from "firebase/app";
import { getMessaging, getToken, onMessage } from "firebase/messaging";
import api from "@/plugins/api.js";

export const firebaseWrapper = {
  firebaseInit: function () {
    const firebaseConfig = {
      apiKey: import.meta.env.VITE_FIREBASE_API_KEY,
      authDomain: import.meta.env.VITE_FIREBASE_AUTH_DOMAIN,
      projectId: import.meta.env.VITE_FIREBASE_PROJECT_ID,
      storageBucket: import.meta.env.VITE_FIREBASE_STORAGE_BUCKET,
      messagingSenderId: import.meta.env.VITE_FIREBASE_MESSAGING_SENDER_ID,
      appId: import.meta.env.VITE_FIREBASE_APP_ID,
    };

    initializeApp(firebaseConfig);

    if ('Notification' in window) {
      const messaging = getMessaging();

      try {
        getToken(messaging, { vapidKey: import.meta.env.VITE_FIREBASE_VAPID_KEY })
          .then((currentToken) => {
            if (currentToken) {
              this.sendTokenToServer(currentToken);
            } else {
              console.warn('Failed to get token.');
            }
          })
          .catch((err) => {
            console.log('An error occurred while retrieving token. ', err);
            this.setTokenSentToServer(false);
          });
      } catch (e) {
        console.log(e);
      }

      onMessage(messaging, (payload) => {
        console.log('Message received. firebase.js ', payload);
        new Notification(payload.notification.title, payload.notification);
      });
    }
  },
  isTokenSentToServer: function (currentToken) {
    return (window.localStorage.getItem('sentFirebaseMessagingToken') === currentToken);
  },
  setTokenSentToServer: function (currentToken) {
    window.localStorage.setItem('sentFirebaseMessagingToken', currentToken ? currentToken : '');
  },
  sendTokenToServer: function (currentToken) {
    if (!this.isTokenSentToServer(currentToken)) {
      api
        .post('firebase/token', {token: currentToken})
        .then((data) => {
          if (data.data.status) {
            this.setTokenSentToServer(currentToken);
          }
        });
    }
  },
};
