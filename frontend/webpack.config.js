const path = require('path');

const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

module.exports = (env, argv) => {

  const generateHtmlWebpackPluginInfo = entry => ({
    inject: false,
    template: './src/' + entry + '/' + entry + '.html',
    filename: path.resolve(__dirname, '..', 'youkok2', 'templates', 'react', (argv.mode === 'development' ? 'dev_' : '') + entry + '.html'),
    chunks: [entry, 'vendors']
  });

  const cssLoaders = argv.mode === 'development' ? [
    'style-loader',
    'css-loader',
    'less-loader',
  ] : [
    MiniCssExtractPlugin.loader,
    'css-loader',
    'less-loader',
  ];

  const htmlPlugin = [
    new HtmlWebpackPlugin(generateHtmlWebpackPluginInfo('frontpage')),
    new HtmlWebpackPlugin(generateHtmlWebpackPluginInfo('search')),
  ];

  const plugins = argv.mode === 'development' ? [ ...htmlPlugin ] : [
    ...htmlPlugin,
    new MiniCssExtractPlugin(),
    new OptimizeCSSAssetsPlugin(),
  ];

  return {
    entry: {
      frontpage: './src/frontpage/frontpage.js',
      search: './src/search/search.js',
    },
    output: {
      // Prefix dev builds with dev. to ignore it
      filename: (argv.mode === 'development' ? 'dev.' : '') +  '[name].js?hash=[contenthash]',
      path: path.resolve(__dirname, '..', 'youkok2', 'public', 'assets', 'apps'),

      // Prefix the dev vendor bundle with dev. to ignore it
      chunkFilename: argv.mode === 'development' ? 'dev.vendor.js' : 'vendor.js',
      pathinfo: false,
      publicPath: 'assets/apps/'
    },
    devtool: argv.mode === 'development' ? 'eval-source-map' : '',
    resolve: {
      extensions: ['.js', '.json', '.jsx']
    },
    watch: argv.mode === 'development',
    module: {
      rules: [
        {
          test: /\.js$/,
          exclude: /node_modules/,
          loader: "babel-loader"
        }, {
          test: /\.(less|css)$/,
          resolve: {
            extensions: [
              '.less',
              '.css'
            ],
          },
          use: cssLoaders,
        }, {
          test: /\.(png|gif|jpe?|eot|svg|ttf|woff|woff2)(\?[a-z0-9=&.]+)?$/,
          loader: 'file-loader',
          options: {
            name: '[name].[ext]',
            outputPath: 'fonts/'
          }
        },
      ]
    },
    optimization: {
      minimizer: argv.mode === 'development' ? [] : [ new UglifyJsPlugin() ],
      splitChunks: {
        cacheGroups: {
          vendors: {
            test: /[\\/]node_modules[\\/]/,
            name: 'vendors',
            enforce: true,
            chunks: 'all'
          }
        }
      },
    },
    plugins: plugins
  }
};