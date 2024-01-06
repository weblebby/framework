/** @type {import("tailwindcss").Config} */
module.exports = {
    prefix: 'fd-',
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.{js,jsx,ts,tsx}',
        './src/**/*.blade.php',
        './src/**/*.{js,jsx,ts,tsx}',
    ],
    theme: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('tailwind-scrollbar-hide'),
    ],
}
