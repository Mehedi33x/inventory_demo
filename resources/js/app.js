import './bootstrap';
import { createApp } from 'vue';
import app from './pages/App.vue';
import router from './router';
import Pagination from './pages/others/Pagination.vue';
import customDirectives from './directives/custom_directives';
import * as Helpers from './helpers/global';
import common_plugs from './plugins/common_plugs';

const vueApp = createApp(app);

vueApp.config.globalProperties.$helpers = Helpers;
vueApp.component('Pagination', Pagination);
vueApp.directive('numbers-only', customDirectives.xNumbersOnly);
vueApp.use(common_plugs);
vueApp.use(router).mount("#app");
