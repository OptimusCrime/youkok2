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
      return ["frontpage"]
      return [entry];
  }
};

const test = ["test1", "test2"];

const generateHtmlWebpackPluginInfo = (argv, entry) => ({
  inject: true,
  scriptLoading: "defer",
  template: `./src/pages/${entry}.html`,
  filename: path.resolve(__dirname, '..', 'static', 'content', `${entry}.html`),
  chunks: filterHtmlWebpackPluginChunks(entry),
});

module.exports = (env, argv) => ({
  entry: "./src/entry.js",
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
  },
  plugins: [
    new HandlebarsPlugin({
      entry: path.join(process.cwd(), "src", "pages", "*.hbs"),
      output: path.join(process.cwd(), "dist", "[name].html"),
      data: {
        SITE_URL: argv.mode === 'development' ? 'http://youkok2.local:8091' : 'https://youkok2.com',
        VERSION: '5.0.0', // TODO
        CHANGELOG: 'TODO' // TODO
      },
      helpers: {
        projectHelpers: path.join(process.cwd(), "helpers", "*.js")
      },
      partials: [
        path.join(process.cwd(), "templates", "*.hbs"),
      ]
    }),
    /*
    ...Object
      .keys(entries)
      .map(key => new HtmlWebpackPlugin(generateHtmlWebpackPluginInfo(argv, key))),
     */
    ...test.map(value => new HtmlWebpackPlugin(generateHtmlWebpackPluginInfo(argv, value))),
    new MiniCssExtractPlugin({
      filename: '[name].[contenthash].css'
    }),
  ]
});
