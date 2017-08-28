var elixir = require('laravel-elixir');
var gulp  = require('gulp');
var shell = require('gulp-shell');
require('laravel-elixir-vueify');
require('laravel-elixir-browsersync-official');
// require('laravel-elixir-livereload');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

elixir(function(mix) {
    mix.less('app.less')
        .combine([
            'public/css/app.css',
            'node_modules/sweetalert2/dist/sweetalert2.min.css',
            'node_modules/bootstrap-calendar/css/calendar.css',
            'node_modules/switchery-browserify/switchery.css',
            'node_modules/nouislider/distribute/nouislider.css',
            'public/custom-icon-font/style.css',
        ], 'public/css/app.css')
        .sass('resbeat.scss')
        .browserify('app.js', null, null, {
            // paths: 'vendor/laravel/spark/resources/assets/js',
        })
        .copy('node_modules/sweetalert2/dist/sweetalert2.min.js', 'public/js/sweetalert.min.js')
        .copy('node_modules/js-md5/build/md5.min.js', 'public/js/md5.min.js')
        // FIXME: When we move to VueJS 2.0, look at switching to this tooltip: https://github.com/Akryum/vue-tooltip (VueJS 2.0)
        // The following is to support https://github.com/samcrosoft/vue-tooltip (VueJS 1.0 only)
        .copy('node_modules/tether/dist/js/tether.min.js', 'public/js/tether/tether.min.js')
        .copy('node_modules/tether-drop/dist/js/drop.min.js', 'public/js/tether-drop/drop.min.js')
        .copy('node_modules/tether-tooltip/dist/js/tooltip.min.js', 'public/js/tether-tooltip/tooltip.min.js')
        .copy('node_modules/vue-tooltip/dist/js/vue-tooltip.js', 'public/js/vue-tooltip/vue-tooltip.js')
        // End tooltip VueJS 1.0 support
        .copy('node_modules/bootstrap-calendar/js/calendar.js', 'public/js/bootstrap-calendar/calendar.js')
        .copy('node_modules/bootstrap-calendar/css/calendar.css', 'public/css/bootstrap-calendar/calendar.css');
        // .livereload();
        // .phpUnit();

    mix.task('clear');

    // BrowserSync
    mix.browserSync({
        proxy: 'resbeat.localhost',
        open:  false,
        files: [
            "resources/assets/css/*.css",
            "resources/assets/js/components/**/*.js",
            "resources/assets/js/components/*.js",
            "resources/assets/js/mixins/**/*.js",
            "resources/assets/js/mixins/*.js",
            "resources/assets/*.js",
            "resources/assets/sass/**/*.scss",
            "resources/views/**/*.blade.php",
            "public/js/app.js",
            "public/css/app.css",
            "public/css/pdf-contract.css",
        ],
        reloadDelay: 500,
        // reloadOnRestart: false,
        // notify:          true
    });
});

gulp.task('clear', shell.task([
    'php artisan clear-compiled',
    'php artisan view:clear'
]));
