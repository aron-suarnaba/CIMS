import vue from '@vitejs/plugin-vue';
import laravel from 'laravel-vite-plugin';
import { defineConfig } from 'vite';
import { fileURLToPath, URL } from 'node:url';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/js/app.js'],
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
    ],
    resolve: {
        alias: {
            '@': fileURLToPath(new URL('./resources/js', import.meta.url)),
            '@css': fileURLToPath(new URL('./resources/css', import.meta.url)),
            '@vendor': fileURLToPath(new URL('./vendor', import.meta.url)),
            'ziggy-js': fileURLToPath(
                new URL('./vendor/tightenco/ziggy', import.meta.url),
            ),
            vue: 'vue/dist/vue.esm-bundler.js',
        },
    },
});
