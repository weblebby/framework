const extensions = ['.blade.php', '.js']
const paths = ['./src', './resources']
const content = []

for (let i = 0; i < paths.length; i++) {
    for (let j = 0; j < extensions.length; j++) {
        content.push(`${paths[i]}/**/*${extensions[j]}`)
    }
}

module.exports = {
    content,
    prefix: 'fd-',
    theme: {
        extend: {},
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('tailwind-scrollbar-hide'),
    ],
}
