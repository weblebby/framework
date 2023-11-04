/** @type {import("tailwindcss").Config} */
module.exports = {
    prefix: 'fd-',
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './src/**/*.blade.php',
        './src/**/*.js',
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('tailwind-scrollbar-hide'),
    ],
}
