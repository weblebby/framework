const mix = require('laravel-mix')

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

mix.js('resources/js/feadmin.js', 'js')
    .js('resources/js/navigation.js', 'js')
    .js('resources/js/extensions/form.js', 'extensions/form')
    .postCss('resources/css/feadmin.css', 'css', [require('tailwindcss')])
    .setResourceRoot('vendor/feadmin/')
    .setPublicPath('public/')
    .version()

/**
 * Copy this code and paste your webpack.mix.js for real time updates
 *
 * mix.copyDirectory('vendor/feadmin/framework/public', 'public/vendor/feadmin');
 */

if (!mix.inProduction()) {
    mix.webpackConfig({ devtool: 'source-map' }).sourceMaps()
}
