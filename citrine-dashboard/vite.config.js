import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';

import { fileURLToPath, URL } from 'node:url';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
        // Custom plugin to serve index.html at root, bypassing Laravel placeholder
        {
            name: 'serve-standalone',
            configureServer(server) {
                server.middlewares.use(async (req, res, next) => {
                    if (req.url === '/' || req.url === '/index.html') {
                        const fs = await import('node:fs');
                        const path = await import('node:path');
                        const html = fs.readFileSync(path.resolve(__dirname, 'index.html'), 'utf-8');
                        res.statusCode = 200;
                        res.setHeader('Content-Type', 'text/html');
                        res.end(await server.transformIndexHtml(req.url, html));
                        return;
                    }
                    next();
                });
            }
        },
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
        },
    },
    server: {
        host: '127.0.0.1',
        port: 5175,
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
});

