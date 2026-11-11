const mix = require( 'laravel-mix' );
const BrowserSyncPlugin = require( 'browser-sync-webpack-plugin' );

mix.js( 'resources/js/app.js', 'public/js' )
    .css( 'resources/css/app.css', 'public/css' )
    .version()
    .webpackConfig( {
        plugins: [
            new BrowserSyncPlugin( {
                host: 'localhost',
                port: 3000,
                proxy: 'http://127.0.0.1:8000',
                open: false,
                injectChanges: true,
                reloadDelay: 0,
                files: [
                    'resources/views/**/*.blade.php',
                    'resources/js/**/*.js',
                    'resources/css/**/*.css',
                    'public/**/*.css',
                ],
            } )
        ]
    } );
