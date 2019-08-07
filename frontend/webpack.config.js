const path = require('path');

const UglifyJsPlugin = require('uglifyjs-webpack-plugin');
const HtmlWebpackPlugin = require('html-webpack-plugin');
const OptimizeCSSAssetsPlugin = require("optimize-css-assets-webpack-plugin");
const MiniCssExtractPlugin = require('mini-css-extract-plugin');

const entries = {
  archive: './src/archive/archive.js',
  courses: './src/courses/courses.js',
  frontpage: './src/frontpage/frontpage.js',
  polyfills: './src/polyfills/polyfills.js',
  searchBar: './src/searchBar/searchBar.js',
  sidebarHistory: './src/sidebarHistory/sidebarHistory.js',
  sidebarPopular: './src/sidebarPopular/sidebarPopular.js',
  sidebarPost: './src/sidebarPost/sidebarPost.js',
  youkok2: './src/youkok2/youkok2.js',

  admin: './src/admin/admin.js',
  adminHomeBoxes: './src/adminHomeBoxes/adminHomeBoxes.js',
  adminHomeGraph: './src/adminHomeGraph/adminHomeGraph.js',
  adminFilesPending: './src/adminFilesPending/adminFilesPending.js',
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
    inject: false,
    template: './src/' + entry + '/' + entry + '.html',
    filename: path.resolve(__dirname, '..', 'youkok2', 'templates', 'react', (argv.mode === 'development' ? 'dev_' : '') + entry + '.html'),
    chunks: filterHtmlWebpackPluginChunks(entry)
});

module.exports = (env, argv) => ({
  entry: entries,
  output: {
    // Prefix dev builds with dev. to ignore it
    filename: argv.mode === 'development' ? 'dev.[name].js' : '[name].js?hash=[contenthash]',
    path: path.resolve(__dirname, '..', 'youkok2', 'public', 'assets', 'apps'),

    // Prefix the dev vendor bundles with dev. to ignore it
    chunkFilename: argv.mode === 'development' ? 'dev.[name].js' : '[name].js?hash=[contenthash]',
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
        use: [
          MiniCssExtractPlugin.loader,
          'css-loader',
          'less-loader',
        ],
      }
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
    ...Object
      .keys(entries)
      .map(key => new HtmlWebpackPlugin(generateHtmlWebpackPluginInfo(argv, key))),
    new MiniCssExtractPlugin({
      filename: argv.mode === 'development' ? 'dev.[name].css' : '[name].css?hash=[contenthash]'
    })
  ]
});
