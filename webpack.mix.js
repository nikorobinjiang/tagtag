let mix = require('laravel-mix');

function resolve(dir) {
  return path.join(__dirname, dir)
}

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

// const BundleAnalyzerPlugin = require('webpack-bundle-analyzer').BundleAnalyzerPlugin;
mix.webpackConfig({
    // plugins: [
    //   new BundleAnalyzerPlugin(),
    // ],
    externals: {
      "BMap": "BMap"
    },
    resolve: {
      alias: {
        '@': resolve('resources'),
        // '@agency': resolve('Modules/Agency/Resources'),
      }
    }
  })
  .autoload({
    // jquery: ['$', 'window.jQuery', 'jQuery'], // more than one
    // lodash: '_' // only one
  })
  .styles(
    [
      'resources/static/css/amazeui.css',
      'resources/static/css/admin.css',
      'resources/static/css/custom.css',
    ],
    'public/css/app.css')
  .scripts(
    [
      'resources/static/js/jquery-2.2.2.js',
      'resources/static/js/amazeui.js',
      'resources/static/js/clipboard.js',
      'resources/static/js/custom.js',
      'resources/static/js/handlebars-v4.0.11.js',
      'resources/static/js/app.js',
      'resources/static/js/tools.js',
      'resources/static/js/space_form.js',
    ],
    'public/js/app.js'
  )
  .js('resources/js/main.js', 'public/js')
  .sass('resources/sass/main.scss', 'public/css')
  // .js('Modules/Agency/Resources/js/main.js', 'public/modules/agency/js')
  // .sass('Modules/Agency/Resources/sass/main.scss', 'public/modules/agency/css');

Mix.listen('configReady', (webpackConfig) => {
  // Create SVG sprites
  webpackConfig.module.rules.unshift({
    test: /\.svg$/,
    loader: 'svg-sprite-loader',
    include: [resolve('resources/js/icons')],
    options: {
      symbolId: 'icon-[name]',
    }
  });

  // Exclude 'svg' folder from font loader
  let imageLoaderConfig = webpackConfig.module.rules.find(rule => String(rule.test) === String(/(\.(png|jpe?g|gif)$|^((?!font).)*\.svg$)/));
  imageLoaderConfig.exclude = [resolve('resources/js/icons'), resolve('public')];
});