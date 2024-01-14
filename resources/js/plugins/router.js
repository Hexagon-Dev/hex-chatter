import { createRouter, createWebHistory } from "vue-router";
import {useUserStore} from '@/stores/userStore.js';

const router = createRouter({
  history: createWebHistory('/'),
  routes: [
    {
      path: '/',
      name: 'index',
      component: () => import('../pages/IndexPage.vue'),
      meta: { title: 'Home' },
    },
    {
      path: '/login',
      name: 'login',
      component: () => import('../pages/LoginPage.vue'),
      meta: { title: 'Login' },
    },
    {
      path: '/register',
      name: 'register',
      component: () => import('../pages/RegisterPage.vue'),
      meta: { title: 'Register' },
    },
    {
      path: '/dashboard',
      name: 'dashboard',
      component: () => import('../pages/DashboardPage.vue'),
      meta: { title: 'Dashboard', requiresAuth: true },
    },
  ],
});

router.beforeEach((to, from, next) => {
  document.title = to.meta.title ? 'HexChatter' + ' | ' + to.meta.title : 'HexChatter';

  const userStore = useUserStore();

  if (to.matched.some(record => record.meta.requiresAuth) && !userStore.isAuthenticated) {
    next({ name: 'login' });
  }

  if (
    to.matched.some(record => record.meta.roles)
    && !to.meta.roles.filter(value => userStore.user.roles.includes(value))
  ) {
    next({ name: 'dashboard' });
  }

  next();
});

export default router;
