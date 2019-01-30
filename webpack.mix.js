const {mix} = require('laravel-mix');
const CleanWebpackPlugin = require('clean-webpack-plugin');

// paths to clean
var pathsToClean = [
    'public/assets/app/js',
    'public/assets/app/css',
    'public/assets/admin/js',
    'public/assets/admin/css',
    'public/assets/auth/css',
];

// the clean options to use
var cleanOptions = {};

mix.webpackConfig({
    plugins: [
        new CleanWebpackPlugin(pathsToClean, cleanOptions)
    ]
});

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for the application as well as bundling up all the JS files.
 |
 */

/*
 |--------------------------------------------------------------------------
 | Core
 |--------------------------------------------------------------------------
 |
 */

mix.js([
    'resources/assets/js/app.js',
    'node_modules/jquery/dist/jquery.js',
    'node_modules/pace-progress/pace.js',

], 'public/assets/app/js/app.js').version();

mix.styles([
    'node_modules/font-awesome/css/font-awesome.css',
    'node_modules/pace-progress/themes/blue/pace-theme-minimal.css',
    'node_modules/gentelella/vendors/iCheck/skins/flat/green.css',
], 'public/assets/app/css/app.css').version();

mix.copy([
    'node_modules/font-awesome/fonts/',
], 'public/assets/app/fonts');

/*
 |--------------------------------------------------------------------------
 | Auth
 |--------------------------------------------------------------------------
 |
 */

mix.styles('resources/assets/auth/css/login.css', 'public/assets/auth/css/login.css').version();
mix.styles('resources/assets/auth/css/register.css', 'public/assets/auth/css/register.css').version();
mix.styles('resources/assets/auth/css/passwords.css', 'public/assets/auth/css/passwords.css').version();

mix.styles([
    'node_modules/bootstrap/dist/css/bootstrap.css',
    'node_modules/gentelella/vendors/animate.css/animate.css',
    'node_modules/gentelella/build/css/custom.css',
], 'public/assets/auth/css/auth.css').version();

/*
 |--------------------------------------------------------------------------
 | Admin
 |--------------------------------------------------------------------------
 |
 */

mix.scripts([
    'node_modules/gentelella/vendors/bootstrap-progressbar/bootstrap-progressbar.min.js',
    'node_modules/gentelella/build/js/custom.js',
], 'public/assets/admin/js/admin.js').version();

mix.styles([
    'node_modules/bootstrap/dist/css/bootstrap.css',
    'node_modules/gentelella/vendors/animate.css/animate.css',
    'node_modules/gentelella/build/css/custom.css',
], 'public/assets/admin/css/admin.css').version();


mix.copy([
    'node_modules/gentelella/vendors/bootstrap/dist/fonts',
], 'public/assets/admin/fonts');


mix.scripts([
    'node_modules/select2/dist/js/select2.full.js',
    'resources/assets/admin/js/users/edit.js',
], 'public/assets/admin/js/users/edit.js').version();

mix.styles([
    'node_modules/select2/dist/css/select2.css',
], 'public/assets/admin/css/users/edit.css').version();

mix.scripts([
    'node_modules/gentelella/vendors/Flot/jquery.flot.js',
    'node_modules/gentelella/vendors/Flot/jquery.flot.time.js',
    'node_modules/gentelella/vendors/Flot/jquery.flot.pie.js',
    'node_modules/gentelella/vendors/Flot/jquery.flot.stack.js',
    'node_modules/gentelella/vendors/Flot/jquery.flot.resize.js',

    'node_modules/gentelella/vendors/flot.orderbars/js/jquery.flot.orderBars.js',
    'node_modules/gentelella/vendors/DateJS/build/date.js',
    'node_modules/gentelella/vendors/flot.curvedlines/curvedLines.js',
    'node_modules/gentelella/vendors/flot-spline/js/jquery.flot.spline.min.js',

    'node_modules/gentelella/production/js/moment/moment.min.js',
    'node_modules/gentelella/vendors/bootstrap-daterangepicker/daterangepicker.js',


    'node_modules/gentelella/vendors/Chart.js/dist/Chart.js',
    'node_modules/jcarousel/dist/jquery.jcarousel.min.js'


], 'public/assets/admin/js/dashboard.js').version();

mix.styles([
    'node_modules/gentelella/vendors/bootstrap-daterangepicker/daterangepicker.css',
    'resources/assets/admin/css/dashboard.css',
], 'public/assets/admin/css/dashboard.css').version();

// USER
mix.scripts([
    'node_modules/datatables.net/js/jquery.dataTables.js',
], 'public/assets/admin/js/users/index.js').version();

mix.styles([
    'resources/assets/admin/css/jquery.datatables.min.css',
], 'public/assets/admin/css/users/index.css').version();

// DATATABLES
mix.scripts([
    'node_modules/datatables.net/js/jquery.dataTables.js',
    'node_modules/gentelella/vendors/datatables.net-responsive/js/dataTables.responsive.min.js',
    'node_modules/gentelella/production/js/moment/moment.min.js',
], 'public/assets/admin/js/datatables.js').version();

mix.styles([
    'node_modules/gentelella/vendors/datatables.net-responsive-bs/css/responsive.bootstrap.min.css',
    'resources/assets/admin/css/jquery.datatables.min.css',
], 'public/assets/admin/css/datatables.css').version();

// BOOTSTRAP DATETIMEPICKER
mix.scripts([
    'node_modules/gentelella/vendors/moment/moment.js',
    'node_modules/gentelella/vendors/bootstrap-datetimepicker/build/js/bootstrap-datetimepicker.min.js'
], 'public/assets/admin/js/bootstrap-datetimepicker.js').version();

mix.styles([
    'node_modules/gentelella/vendors/bootstrap-datetimepicker/build/css/bootstrap-datetimepicker.css',
], 'public/assets/admin/css/bootstrap-datetimepicker.css').version();

// SELECT2
mix.styles([
    'node_modules/gentelella/vendors/select2/dist/css/select2.css',
], 'public/assets/admin/css/select2.css').version();

mix.scripts([
    'node_modules/select2/dist/js/select2.full.js'
], 'public/assets/admin/js/select2.js').version();

// AUTO NUMERIC FOR PRICE
mix.scripts([
    'node_modules/autonumeric/dist/autoNumeric.min.js'
], 'public/assets/admin/js/autonumeric.js').version();

// STRING BUILDER
mix.scripts([
    'node_modules/strbuilder/dist/stringbuilder.js'
], 'public/assets/admin/js/stringbuilder.js').version();

// BOOTSTRAP FILEINPUT
mix.scripts([
    'vendor/kartik-v/bootstrap-fileinput/js/fileinput.js'
], 'public/assets/admin/js/fileinput.js').version();

mix.styles([
    'vendor/kartik-v/bootstrap-fileinput/css/fileinput.css',
], 'public/assets/admin/css/fileinput.css').version();

// REQUIRE JS
mix.scripts([
    'node_modules/requirejs/bin/r.js'
], 'public/assets/admin/js/r.js').version();

mix.copyDirectory('resources/views/images', 'public/assets/admin/images');

/*
 |--------------------------------------------------------------------------
 | Frontend
 |--------------------------------------------------------------------------
 |
 */
