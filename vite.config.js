import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import livewire from '@defstudio/vite-livewire-plugin';

export default defineConfig({
    server: {
        watch: {
            ignored: ['**/vendor/**', '**/node_modules/**', '**/.git/**'],
        },
    },
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: false,
        }),
        livewire({
            refresh: ['resources/css/app.css'],
        }),
    ],
});
