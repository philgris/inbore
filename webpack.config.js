var Encore = require('@symfony/webpack-encore');

// Manually configure the runtime environment if not already configured yet by the "encore" command.
// It's useful when you use tools that rely on webpack.config.js file.
if (!Encore.isRuntimeEnvironmentConfigured()) {
  Encore.configureRuntimeEnvironment(process.env.NODE_ENV || 'dev');
}

Encore
// directory where compiled assets will be stored
    .setOutputPath('public/build/')
    // public path used by the web server to access the output path
    .setPublicPath('/build')
    // .setPublicPath('/grands-singes/public/build')
    // only needed for CDN's or sub-directory deploy
    .setManifestKeyPrefix(Encore.isProduction() ? 'public/build' : 'build')

  /*
   * ENTRY CONFIG
   *
   * Add 1 entry for each "page" of your app
   * (including one that's included on every page - e.g. "app")
   *
   * Each entry will result in one JavaScript file (e.g. app.js)
   * and one CSS file (e.g. app.css) if your JavaScript imports CSS.
   */

  // Core assets
  .addEntry('core', './assets/Core/js/core.js')
  .addEntry('dashboard', './assets/Core/js/dashboard.js')
  .addStyleEntry('login', './assets/Core/css/login.less')
  // Index pages assets
  .addEntry('index', './assets/Core/js/index.js')

  // Core forms assets
  .addEntry('forms', './assets/Core/forms/js/forms.js')
  .addEntry('form-tools', './assets/Core/forms/js/form-tools.js')
  .addEntry('vocabulary-form', './assets/Core/forms/js/vocabulary-form.js')
  
  // Query builder
  // .addEntry('querybuilder', './assets/QueryBuilder/js/main.js')

  // When enabled, Webpack "splits" your files into smaller pieces for greater optimization.
  .splitEntryChunks()

  // will require an extra script tag for runtime.js
  // but, you probably want this, unless you're building a single-page app
  .enableSingleRuntimeChunk()

  /*
   * FEATURE CONFIG
   *
   * Enable & configure other features below. For a full
   * list of features, see:
   * https://symfony.com/doc/current/frontend.html#adding-more-features
   */
  .cleanupOutputBeforeBuild()
  .enableBuildNotifications()
  .enableSourceMaps(!Encore.isProduction())
  // enables hashed filenames (e.g. app.abc123.css)
  .enableVersioning(Encore.isProduction())

  // enables @babel/preset-env polyfills
  .configureBabelPresetEnv((config) => {
    config.useBuiltIns = 'usage';
    config.corejs = 3;
  })

  // enables Sass/SCSS support
  .enableSassLoader()

  // enables Less support
  .enableLessLoader()

  // enables VueJS
  //.enableVueLoader()
  //.addRule({
  //  resourceQuery: /blockType=i18n/,
  //  type: 'javascript/auto',
  //  loader: '@intlify/vue-i18n-loader',
  // })

  // YAML loader
  .addLoader({ test: /\.ya?ml$/, loader: 'js-yaml-loader' })

  // uncomment if you use TypeScript
  //.enableTypeScriptLoader()

  // uncomment to get integrity="..." attributes on your script & link tags
  // requires WebpackEncoreBundle 1.4 or higher
  //.enableIntegrityHashes(Encore.isProduction())

  // uncomment if you're having problems with a jQuery plugin
  .autoProvidejQuery()
  // jQuery Datatables loader
  // .addLoader({ test: /datatables\.net.*/, loader: 'imports-loader?define=>false' })

  // Provide L namespace for leaflet 
  .autoProvideVariables({
    L: "leaflet",
  })

  // uncomment if you use API Platform Admin (composer req api-admin)
  //.enableReactPreset()
  //.addEntry('admin', './assets/js/admin.js')

  // Copy image directory
  .copyFiles({
    from: './assets/images',
    to: 'images/[name].[ext]'
  })
  // Copy import templates directory
  .copyFiles({
    from: './assets/imports',
    to: 'imports/[name].[ext]'
  })
  // Copy documents directory
  .copyFiles({
    from: './docs',
    to: 'docs/[path][name].[ext]'
  })
  
  // Export routes and translations to public directory to be exposed on the JS side
  // .addPlugin(
  //   new WebpackShellPluginNext({
  //    onBuildStart: {
  //      scripts: [
  //        "symfony console fos:js-routing:dump --format=json --target=public/js/routes.json",
  //      ],
  //      blocking: true,
  //      parallel: false,
  //    },
  //  })
  // );
  

var config = Encore.getWebpackConfig();
// disable amd, for datatable
config.module.rules.unshift({
  parser: {
    amd: false
  }
});


module.exports = config;
