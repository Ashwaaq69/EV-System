import './bootstrap';
import '../css/app.css';
import { createApp, h } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import router from './router';
import StandaloneApp from './StandaloneApp.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Check if we are running the standalone Vite server (no Inertia root)
const isStandalone = !document.getElementById('app')?.dataset.page;

if (isStandalone) {
    const app = createApp(StandaloneApp);
    app.use(router);
    // Mock Inertia properties globally for standalone mode
    app.config.globalProperties.$page = {
        url: window.location.pathname,
        props: { auth: { user: { name: 'Citrine Client', email: 'client@citrineos.com', role: 'client' } } }
    };
    app.mount('#app');
} else {
    createInertiaApp({
        title: (title) => `${title} - ${appName}`,
        resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
        setup({ el, App, props, plugin }) {
            return createApp({ render: () => h(App, props) })
                .use(plugin)
                .use(ZiggyVue)
                .mount(el);
        },
        progress: {
            color: '#4B5563',
        },
    });
}
