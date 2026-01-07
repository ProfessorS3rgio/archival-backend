import './bootstrap';
import '@/assets/main.css';
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'
import ThemeProvider from '@/ComponentsOld/layout/ThemeProvider.vue'
import VueSweetalert2 from 'vue-sweetalert2';
import 'sweetalert2/dist/sweetalert2.min.css';
import PrimeVue from 'primevue/config';
import 'primeicons/primeicons.css';
import { ZiggyVue } from '../../vendor/tightenco/ziggy/dist/index.esm.js'
import { Ziggy } from './ziggy.js'

createInertiaApp({
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
    return pages[`./Pages/${name}.vue`]
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(ThemeProvider, {}, { default: () => h(App, props) }) })
      .use(plugin)
      .use(ZiggyVue, Ziggy)
      .use(VueSweetalert2)
      .use(PrimeVue)
      .mount(el)
  },
})

// Add alias for @ imports
import.meta.glob(['./Components/**/*.vue', './Layouts/**/*.vue'], { eager: true })
