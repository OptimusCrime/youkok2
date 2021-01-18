const path = require('path'),
  TerserWebpackPlugin = require('terser-webpack-plugin'),
  HtmlWebpackPlugin = require('html-webpack-plugin'),
  HandlebarsWebpackPlugin = require("handlebars-webpack-plugin"),
  OptimizeCssAssetsWebpackPlugin = require("optimize-css-assets-webpack-plugin"),
  MiniCssExtractPlugin = require('mini-css-extract-plugin');

/**
 * Read the rant in the other webpack.config.js file found in the frontend directory.
 */

const pages = [
  "404",
  "500",
  "about",
  "archive",
  "changelog",
  "courses",
  "frontpage",
  "help",
  "login",
  "terms"
];

const generateHtmlWebpackPluginInfo = entry => ({
  inject: true,
  scriptLoading: "defer",
  template: `./dist/${entry}.html`,
  filename: path.resolve(__dirname, '..', 'static', 'content', 'admin', `${entry}.html`),
  chunks: ["main"],
});

module.exports = (env, argv) => ({
  entry: "./src/entry.js",
  output: {
    filename: '[name].[contenthash].js',
    path: path.resolve(__dirname, '..', 'static', 'content', 'admin', 'static'),
    pathinfo: false,
    publicPath: '/admin/static'
  },
  devtool: argv.mode === 'development' ? 'eval-source-map' : false,
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
    ]
  },
  optimization: {
    minimize: argv.mode !== 'development',
    minimizer: argv.mode === 'development' ? [] : [new TerserWebpackPlugin(), new OptimizeCssAssetsWebpackPlugin()],
  },
  plugins: [
    new HandlebarsWebpackPlugin({
      entry: path.join(process.cwd(), "src", "pages", "*.hbs"),
      output: path.join(process.cwd(), "dist", "[name].html"),
      data: {
        SITE_URL: argv.mode === 'development' ? 'http://youkok2.local:8091' : 'https://youkok2.com',
      },
      helpers: {
        projectHelpers: path.join(process.cwd(), "helpers", "*.js")
      },
      partials: [
        path.join(process.cwd(), "partials", "*.hbs"),
      ]
    }),
    ...pages
      .map(page => new HtmlWebpackPlugin(generateHtmlWebpackPluginInfo(page))),
    new MiniCssExtractPlugin({
      filename: '[name].[contenthash].css'
    }),
  ]
});
