const mix = require('laravel-mix');

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

mix.js('resources/js/app.js', 'public/js')
    .js('resources/js/vue.js', 'public/js')
    .js('resources/js/reg.js', 'public/js')
    .js('resources/js/chart.js', 'public/js')
    // .js('resources/js/forum.js', 'public/js')
    .sass('resources/sass/chart.scss', 'public/css')
    .sass('resources/sass/mahouka.scss', 'public/css')
    .sass('resources/sass/forum.scss', 'public/css')
    .sass('resources/sass/app.scss', 'public/css');
