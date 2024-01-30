import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
    base: '/weblebby/',
    plugins: [
        laravel({
            input: [
                './resources/css/weblebby.css',
                './resources/js/weblebby.js',
                './resources/js/navigation.js',
                './resources/js/code-editor.js',
                './resources/js/pages/post/post.js',
            ],
            refresh: true,
        }),
    ],
})
