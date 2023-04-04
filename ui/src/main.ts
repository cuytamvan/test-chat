import { createApp } from 'vue';
import { createPinia } from 'pinia';

import './assets/css/main.css';

import App from './App.vue';
import Router from './router';
import Config from './services/config';

import useAuthStore from '@/stores/auth.store';

const app = createApp(App);

app.config.globalProperties.$config = Config;

app.use(Router);
app.use(createPinia());

Router.beforeEach(async (to, from, next) => {
  const authStore = useAuthStore();

  await authStore.checkUser();

  console.log(authStore.isLoggedin);

  if (to.meta.auth && !authStore.isLoggedin) next('/login');
  else if (to.meta.guest && authStore.isLoggedin) next('/');
  else next();
});

app.mount('#app');
