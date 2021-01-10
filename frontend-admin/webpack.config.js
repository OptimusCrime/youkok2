const path = require('path');

const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");
const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const HandlebarsPlugin = require("handlebars-webpack-plugin");

const entries = {
  //archive: './src/archive/archive.js',
  //courses: './src/courses/courses.js',
  frontpage: './src/frontpage/frontpage.js',
  //polyfills: './src/polyfills/polyfills.js',
  //searchBar: './src/searchBar/searchBar.js',
  //sidebarHistory: './src/sidebarHistory/sidebarHistory.js',
  //sidebarPopular: './src/sidebarPopular/sidebarPopular.js',
  //sidebarPost: './src/sidebarPost/sidebarPost.js',
  //youkok2: './src/youkok2/youkok2.js',

  //admin: './src/admin/admin.js',
  //adminHomeBoxes: './src/adminHomeBoxes/adminHomeBoxes.js',
  //adminHomeGraph: './src/adminHomeGraph/adminHomeGraph.js',
  //adminFiles: './src/adminFiles/adminFiles.js',
  //adminFilesPending: './src/adminFilesPending/adminFilesPending.js',
  //adminDiagnosticsCache: './src/adminDiagnosticsCache/adminDiagnosticsCache.js',
};

const filterHtmlWebpackPluginChunks = entry => {
  switch (entry) {
    case 'youkok2':
      return [entry, 'vendors'];
    case 'admin':
      return [entry, 'vendorsAdmin'];
    default:
      return [entry];
  }
};

const generateHtmlWebpackPluginInfo = (argv, entry) => ({
  inject: "head",
  template: `./src/${entry}${entry}.hbs`,
  filename: path.resolve(__dirname, '..', 'static', 'content', `${entry}.html`),
  chunks: filterHtmlWebpackPluginChunks(entry),
});

module.exports = (env, argv) => ({
  entry: entries,
  output: {
    // Prefix dev builds with dev. to ignore it
    filename: '[name].[contenthash].js',
    path: path.resolve(__dirname, '..', 'static', 'content'),

    // Prefix the dev vendor bundles with dev. to ignore it
    chunkFilename: '[name].[contenthash].js',
    pathinfo: false,
    publicPath: '/static/'
  },
  devtool: argv.mode === 'development' ? 'eval-source-map' : '',
  resolve: {
    extensions: ['.js', '.json', '.jsx', '.hbs']
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
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader',
          'less-loader',
        ],
      },
      /*{
        test: /\.hbs$/,
        loader: "handlebars-loader",
        options: {
          //runtime: path.join(__dirname, 'handlebars-runtime.js'),
          partialDirs: [
            path.join(__dirname, 'templates')
          ],
          helperDirs: [
            path.join(__dirname, 'helpers')
          ]
        },
      }*/
    ]
  },
  optimization: {
    minimizer: argv.mode === 'development' ? [] : [new UglifyJsPlugin(), new OptimizeCSSAssetsPlugin()],
    splitChunks: {
      cacheGroups: {
        vendorsAdmin: {
          test: /[\\/]node_modules[\\/](highcharts|react-highcharts)[\\/]/,
          name: 'vendorsAdmin',
          enforce: true,
          chunks: 'all',
          priority: 999,
        },
        vendors: {
          test: /[\\/]node_modules[\\/]/,
          name: 'vendors',
          enforce: true,
          chunks: 'all',
          priority: 1,
        }
      }
    },
  },
  plugins: [
    new HandlebarsPlugin({
      entry: path.join(process.cwd(), "src", "*", "*.hbs"),
      output: path.join(process.cwd(), "dist", "[name].html"),
      data: {
        SITE_URL: argv.mode === 'development' ? 'http://youkok2.local:8091' : 'https://youkok2.com',
        VERSION: '5.0.0', // TODO
      },
      helpers: {
        projectHelpers: path.join(process.cwd(), "helpers", "*.js")
      },
      partials: [
        path.join(process.cwd(), "templates", "*.hbs"),
      ]
    }),
    ...Object
      .keys(entries)
      .map(key => new HtmlWebpackPlugin(generateHtmlWebpackPluginInfo(argv, key))),
    new MiniCssExtractPlugin({
      filename: '[name].[contenthash].css'
    }),
  ]
});
