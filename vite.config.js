import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/feadmin.css',
                'resources/js/feadmin.js',
                'resources/js/navigation.js',
            ],
            buildDirectory: '/../public',
            refresh: true,
        }),
    ],
})