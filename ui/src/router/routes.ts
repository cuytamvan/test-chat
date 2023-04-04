import { RouteRecordRaw } from 'vue-router';

const Routes: RouteRecordRaw[] = [
  {
    name: 'home',
    path: '/',
    component: () => import('@/pages/Home.vue'),
    meta: { auth: true },
  },
  {
    name: 'login',
    path: '/login',
    component: () => import('@/pages/Login.vue'),
    meta: { guest: true },
  },
];

export default Routes;
