import {defineConfig} from 'vite';
import laravel from 'laravel-vite-plugin';
import vue from '@vitejs/plugin-vue';
import path from 'path';

export default defineConfig({
    server: {
        host: process.env.VITE_HOST || '0.0.0.0',
        port: Number(process.env.VITE_PORT) || 5173,
        strictPort: true,
        cors: {
            origin: ['http://localhost', 'http://127.0.0.1'],
            credentials: true,
        },
        hmr: {
            host: process.env.VITE_HMR_HOST || 'localhost',
            port: Number(process.env.VITE_PORT) || 5173,
        },
        origin:
            process.env.VITE_DEV_SERVER_URL ||
            `http://${process.env.VITE_HMR_HOST || 'localhost'}:${Number(process.env.VITE_PORT) || 5173}`,
    },
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        vue(),
    ],
    resolve: {
        alias: {
            '@': path.resolve(__dirname, 'resources/js'),
        },
    },
});
