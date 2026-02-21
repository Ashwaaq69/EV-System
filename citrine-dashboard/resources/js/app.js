import './bootstrap';
import '../css/app.css';
import { createApp, h } from 'vue';
import { createInertiaApp, router as inertiaRouter } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import router from './router';
import StandaloneApp from './StandaloneApp.vue';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

// Check if we are running the standalone Vite server (no Inertia root)
const isStandalone = !document.getElementById('app')?.dataset.page;
console.log('[Citrine] Booting...', isStandalone ? 'Standalone Mode' : 'Inertia Mode');

if (isStandalone) {
    const app = createApp(StandaloneApp);
    
    // 1. Define Mocks
    const headManagerMock = {
        createProvider: () => ({
            update: () => {},
            disconnect: () => {},
        }),
    };

    const mockPage = {
        url: window.location.pathname,
        props: { 
            auth: { user: { name: 'Citrine Client', email: 'client@citrineos.com', role: 'client' } },
            errors: {},
            status: null,
            flash: {},
            canResetPassword: true
        }
    };

    const mockInertia = {
        visit: (url) => { window.location.href = url; },
        post: (url) => { console.log('Mock POST to', url); return { on: () => {} }; },
        put: (url) => console.log('Mock PUT to', url),
        patch: (url) => console.log('Mock PATCH to', url),
        delete: (url) => console.log('Mock DELETE to', url),
        reload: () => window.location.reload(),
        replace: (url) => window.location.replace(url),
        on: () => (() => {}),
        headManager: headManagerMock,
        page: mockPage,
    };

    // 2. Set Global Properties (for Options API & Templates)
    app.config.globalProperties.$inertia = mockInertia;
    app.config.globalProperties.$page = mockPage;
    app.config.globalProperties.headManager = headManagerMock;
    app.config.globalProperties.route = (name) => {
        const routes = {
            'login': '/login', 'register': '/register', 'logout': '/logout',
            'home': '/', 'dashboard': '/dashboard', 'password.request': '/forgot-password',
            'password.email': '/forgot-password',
        };
        return routes[name] || '/';
    };

    // 3. Provide (for Composition API / useForm / Head)
    app.provide('$inertia', mockInertia);
    app.provide('$page', mockPage);
    app.provide('page', mockPage);
    app.provide('headManager', headManagerMock);

    // 4. Force Global Component Overrides for Standalone
    app.component('Head', { 
        props: ['title'],
        data() { 
            return { provider: { update: () => {}, disconnect: () => {} } } 
        },
        render() { return null } 
    });
    app.component('Link', { 
        props: ['href', 'method', 'data', 'as', 'type'], 
        setup(props, { slots }) {
            return () => h(props.as || 'a', { 
                href: props.href,
                type: props.type,
                onClick: (e) => {
                    if (props.href?.startsWith('/') && (!props.method || props.method === 'get')) {
                        e.preventDefault();
                        router.push(props.href);
                    }
                }
            }, slots.default?.());
        }
    });

    // Provide usePage mock
    window.usePage = () => mockPage;
    window.useForm = (data) => {
        // Simple form mock for standalone
        return {
            ...data,
            errors: {},
            processing: false,
            post: (url) => console.log('Standalone POST to', url),
            put: (url) => console.log('Standalone PUT to', url),
            patch: (url) => console.log('Standalone PATCH to', url),
            delete: (url) => console.log('Standalone DELETE to', url),
            reset: () => {},
            clearErrors: () => {},
            setError: () => {},
        };
    };

    // 5. Initialize Router and Mount
    app.use(router);

    // 6. Patch the real Inertia router to prevent crashes when hooks like useForm call it
    // This satisfying the 'url' property access in Router.visit
    inertiaRouter.page = mockPage;
    inertiaRouter.visit = (url, options = {}) => {
        console.log('Standalone Inertia visit to:', url);
        
        // Simulate successful Auth actions in standalone mode
        const isAuthAction = (typeof url === 'string') && (
            url.includes('/login') || 
            url.includes('/register') || 
            url.includes('/reset-password')
        );

        if (isAuthAction && options.method?.toLowerCase() === 'post') {
            const role = mockPage.props.auth.user.role;
            const target = role === 'admin' ? '/dashboard' : '/client/portal';
            console.log(`Simulating successful Auth action for ${role}, redirecting to ${target}...`);
            setTimeout(() => {
                router.push(target);
            }, 500);
            return { on: () => {} };
        }

        if (typeof url === 'string' && url.startsWith('/')) {
            router.push(url);
        } else {
            window.location.href = url;
        }
        return { on: () => {} };
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
            console.log('[Citrine] Inertia App Mounted');
        },
        progress: {
            color: '#4B5563',
        },
    });
}
