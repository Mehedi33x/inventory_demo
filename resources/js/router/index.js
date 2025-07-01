import { createRouter, createWebHistory } from 'vue-router';

const routes = [
    {
        path: '/',
        name: 'home',
        component: () => import('../pages/home/Home.vue'),
    },
    // products
    {
        path: '/product/list',
        name: 'product.index',
        component: () => import('../pages/product/index.vue'),
    },
    {
        path: '/product/create',
        name: 'product.create',
        component: () => import('../pages/product/create.vue'),
    },
    {
        path: '/product/edit/:id',
        name: 'product.edit',
        component: () => import('../pages/product/create.vue'),
    },
    // users
    {
        path: '/user/list',
        name: 'user.index',
        component: () => import('../pages/user/index.vue'),
    },
    {
        path: '/user/create',
        name: 'user.create',
        component: () => import('../pages/user/create.vue'),
    },
    // auth
    {
        path: '/login',
        name: 'auth.login',
        component: () => import('../pages/auth/login.vue'),

    },
    {
        path: '/register',
        name: 'auth.register',
        component: () => import('../pages/auth/register.vue'),

    },
    // stock
    {
        path: '/stock',
        name: 'stock.index',
        component: () => import('../pages/stock/index.vue'),
    },
    {
        path: '/stock/create',
        name: 'stock.create',
        component: () => import('../pages/stock/create.vue'),
    },
    {
        path: '/stock/edit/:id',
        name: 'stock.edit',
        component: () => import('../pages/stock/create.vue'),
    },
    // sale
    {
        path: '/sale',
        name: 'sale.index',
        component: () => import('../pages/sale/index.vue'),
    },
    {
        path: '/sale/create',
        name: 'sale.create',
        component: () => import('../pages/sale/create.vue'),
    },
    {
        path: '/sale/edit/:id',
        name: 'sale.edit',
        component: () => import('../pages/sale/create.vue'),
    },

    // report
    {
        path: '/report',
        name: 'report.index',
        component: () => import('../pages/report/report.vue'),
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
