import { defineConfig } from 'vite';
import laravel, { refreshPaths } from 'laravel-vite-plugin';

export default defineConfig({
    base: '/build/', // << ini penting untuk Vercel!
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/front.css',
                'resources/js/app.js',
            ],
            refresh: [
                ...refreshPaths,
                'app/Http/Livewire/**',
            ],
        }),
    ],
});