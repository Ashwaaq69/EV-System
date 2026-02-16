// import { defineConfig } from 'vite';
// import laravel from 'laravel-vite-plugin';
// import tailwindcss from '@tailwindcss/vite';

// export default defineConfig({
//     plugins: [
//         laravel({
//             input: ['resources/css/app.css', 'resources/js/app.js'],
//             refresh: true,
//         }),
//         tailwindcss(),
//     ],
//     server: {
//         watch: {
//             ignored: ['**/storage/framework/views/**'],
//         },
//     },
// });




import { defineConfig } from 'vite';
import vue from '@vitejs/plugin-vue';
import { fileURLToPath, URL } from 'node:url';

export default defineConfig({
    plugins: [
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
        },
    },
    server: {
        port: 5175,
        strictPort: true,
        cors: true,
        // Proxy /api and Laravel auth routes to the Laravel backend when running standalone Vite dev
        proxy: {
            '/api': {
                target: process.env.VITE_BACKEND_URL || 'http://127.0.0.1:8000',
                changeOrigin: true,
            },
            '/sanctum': {
                target: process.env.VITE_BACKEND_URL || 'http://127.0.0.1:8000',
                changeOrigin: true,
            },
            '/login': { target: process.env.VITE_BACKEND_URL || 'http://127.0.0.1:8000', changeOrigin: true },
            '/register': { target: process.env.VITE_BACKEND_URL || 'http://127.0.0.1:8000', changeOrigin: true },
            '/logout': { target: process.env.VITE_BACKEND_URL || 'http://127.0.0.1:8000', changeOrigin: true },
            '/forgot-password': { target: process.env.VITE_BACKEND_URL || 'http://127.0.0.1:8000', changeOrigin: true },
            '/reset-password': { target: process.env.VITE_BACKEND_URL || 'http://127.0.0.1:8000', changeOrigin: true },
        },
    },
    build: {
        outDir: 'public/dist',
        emptyOutDir: true,
    }
});

