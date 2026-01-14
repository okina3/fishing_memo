import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/user/ajax/ajax-bait-add.js',
                'resources/js/user/ajax/ajax-fish-name-add.js',
                'resources/js/user/ajax/ajax-hook-add.js',
                'resources/js/user/ajax/ajax-rod-add.js',
                'resources/js/user/ajax/ajax-spot-add.js',
                'resources/js/user/areas/bait-area-add.js',
                'resources/js/user/areas/fishing-result-add.js',
                'resources/js/user/areas/hook-area-add.js',
                'resources/js/user/areas/rod-area-add.js',
                'resources/js/user/areas/spot-area-add.js'
            ],
            refresh: true,
        }),
    ],
    server: {
        host: true,
        hmr: {
            host: 'localhost',
        },
    },
});
