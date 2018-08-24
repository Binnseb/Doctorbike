var Encore = require('@symfony/webpack-encore');

Encore

    .autoProvidejQuery()

    // the project directory where compiled assets will be stored
    .setOutputPath('public/build/')

    // the public path used by the web server to access the previous directory
    .setPublicPath('/build')
    .cleanupOutputBeforeBuild()
    .enableSourceMaps(!Encore.isProduction())

    // uncomment to create hashed filenames (e.g. app.abc123.js)
    // .enableVersioning(Encore.isProduction())

    .addEntry('doctorbikejs', './public/js/doctorbike.js')

    .addStyleEntry('doctorbikecss', './public/build/doctorbikecss.css')

    // uncomment if you use Sass/SCSS files
    // .enableSassLoader()

    // uncomment for legacy applications that require $/jQuery as a global variable
    // .autoProvidejQuery()
;

module.exports = Encore.getWebpackConfig();
