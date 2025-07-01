# Laravel & VueJS Integration

This README provides step-by-step instructions to set up a Laravel application integrated with VueJS, using Vite and Vue Router.

---

## Prerequisites
Ensure you have the following installed on your system:
- PHP
- Composer
- Node.js

---

## Setup Instructions

### 1. Install Laravel
Run the following command to create a new Laravel project:
```bash
composer create-project laravel/laravel laravue
```
Replace `laravue` with your desired application name.

Navigate to your project directory:
```bash
cd laravue
```

Serve the application:
```bash
php artisan serve
```
Access the application at [http://127.0.0.1:8000/](http://127.0.0.1:8000/).

### 2. Install VueJS and Dependencies

Install the Vue plugin for Vite:
```bash
npm install --save-dev @vitejs/plugin-vue
```

Install Vue and Vue Router:
```bash
npm install vue vue-router
```

---

### 3. Update `vite.config.js`
Modify the `vite.config.js` file as follows:
```javascript
import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [
        laravel(["resources/js/app.js"]),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
});
```

---

### 4. Update `resources/js/app.js`
Replace the content of `resources/js/app.js` with:
```javascript
import "./bootstrap";
import { createApp } from "vue";
import App from "./App.vue";

const app = createApp(App);
app.mount("#app");
```

---

### 5. Create `App.vue`
Create a file named `App.vue` in the `resources/js` directory with the following content:
```vue
<script setup>
import { ref } from "vue";

const count = ref(0);
</script>

<template>
    <div>
        <h2>Welcome to Laravel & VueJS app!</h2>
        <div>
            <div>{{ count }}</div>
            <button type="button" @click="count++">Add</button>
            <button type="button" @click="count--">Minus</button>
        </div>
    </div>
</template>
```

---

### 6. Add Laravel Route
Update the `routes/web.php` file:
```php
<?php

use Illuminate\Support\Facades\Route;

Route::get('/{any?}', function () {
    return view('app');
})->where('any', '.*');
```

---

### 7. Create Blade File
Create `resources/views/app.blade.php`:
```html
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Laravel and VueJS</title>

    @vite('resources/js/app.js')
</head>
<body>
    <div id="app"></div>
</body>
</html>
```

---

### 8. Run the Application
Run the following commands in separate terminals:
```bash
php artisan serve
```
```bash
npm run dev
```
Visit [http://127.0.0.1:8000/](http://127.0.0.1:8000/) to see the application.

---

## Adding Vue Router

### 1. Create Routes
Create a file `resources/js/routes/index.js`:
```javascript
import { createWebHistory, createRouter } from "vue-router";

const routes = [
    { path: "/", component: () => import("./../pages/HomeView.vue") },
    { path: "/about", component: () => import("./../pages/AboutView.vue") },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

export default router;
```

### 2. Create Pages
Create `resources/js/pages/HomeView.vue`:
```vue
<template>
    This is home page!
</template>
```

Create `resources/js/pages/AboutView.vue`:
```vue
<template>
    This is about page!
</template>
```

### 3. Update `app.js`
Update `resources/js/app.js`:
```javascript
import "./bootstrap";
import { createApp } from "vue";
import App from "./App.vue";
import router from "./routes";

const app = createApp(App);
app.use(router);
app.mount("#app");
```

### 4. Update `App.vue`
Replace the content of `App.vue`:
```vue
<script setup>
import { ref } from "vue";

const count = ref(0);
</script>

<template>
    <div>
        <h2>Welcome to Laravel & VueJS app!</h2>
        <div>
            <div>{{ count }}</div>
            <div style="display: flex; gap: 10px">
                <button type="button" @click="count++">Add</button>
                <button type="button" @click="count--">Minus</button>
            </div>
        </div>

        <div style="display: flex; gap: 10px">
            <router-link to="/">Home</router-link>
            <router-link to="/about">About</router-link>
        </div>

        <router-view></router-view>
    </div>
</template>
```

---

## Final Steps
Visit the application at [http://127.0.0.1:8000/](http://127.0.0.1:8000/) to test the routes.

You can now navigate between the "Home" and "About" pages using the links, and the application functions as a single-page application.
