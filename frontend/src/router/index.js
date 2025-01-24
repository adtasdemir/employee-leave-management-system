import { createRouter, createWebHistory } from 'vue-router';

const Home = () => import('@/pages/HomePage.vue');
const Login = () => import('@/pages/LoginPage.vue');

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  }
];

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
});

router.beforeEach((to, from, next) => {
  const token = localStorage.getItem('token');
  if (token && to.name === 'Login') {
    next({ name: 'Login' });
  } else if (!token && to.name !== 'Login') {
    next({ name: 'Home' });
  } else {
    next();
  }
});

export default router;
