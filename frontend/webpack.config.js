const path = require('path'),
  fs = require('fs'),
  TerserWebpackPlugin = require('terser-webpack-plugin'),
  HtmlWebpackPlugin = require('html-webpack-plugin'),
  HandlebarsWebpackPlugin = require("handlebars-webpack-plugin"),
  OptimizeCssAssetsWebpackPlugin = require("optimize-css-assets-webpack-plugin"),
  MiniCssExtractPlugin = require('mini-css-extract-plugin');

/**
 * This webpack configuration is some of the the worst mess I have ever produced, and I feel sorry for anyone who
 * reads this. Let me try to explain what is going on. This block also serves as a readme to my future self, because I
 * will, at some point, find this and wonder what the heck is going on.
 *
 * The main issue I faced with html-webpack-plugin was their inability to use a better template engine than underscore.
 * They say custom template engines are supported, but trying to get html-webpack-plugin to work with handlebars-loader
 * took me forever, and it never worked like it should. Handlebars in it self is also an issue. Their poor support for
 * common template functionality like extending another template require usage of plugins. Doing this in a customized
 * runtime environment always threw unexpected errors, and I could never get it to work properly. Because of that, the
 * template had to be split into separate top and bottom chunks.
 *
 * Alright. So I rewrote the code from having a bunch of entrypoints, to having just one. This required some boilerplate
 * code for bootstrapping, but it was not the worst thing in the world. After Webpack transpiles the js file and css
 * file, HandlebarsPlugin will parse all the .hbs templates located in ./src/pages, using the partials in ./partials and
 * some of the custom helpers I had to implement in ./helpers. This results in corresponding HTML files that are
 * outputted in ./dist.
 *
 * After HandlebarsPlugin is finished executing, a bunch of html-webpack-plguins are executed, each targeting one of
 * the rendered templates found in ./dist. These templates have the js and css files injected into their head and body,
 * and the resulting HTML file is then finally stored in the nginx document root.
 *
 * This is a total mess, but I could not figure out a better way of doing it. Because we now rely on completely static
 * templates, they have to be rendered in completion before being served directly by Nginx. It was easier to use Twig
 * to assemble the templates on request server side, but this results in unwanted overhead.
 */

const VERSION = fs.readFileSync(path.resolve(__dirname, '..', 'VERSION'), { encoding: 'utf-8'}).trim();
const CHANGELOG = fs.readFileSync(path.resolve(__dirname, '..', 'CHANGELOG.md'), { encoding: 'utf-8'}).trim();

const pages = [
  "404",
  "500",
  "about",
  "archive",
  "changelog",
  "courses",
  "frontpage",
  "help",
  "terms"
];

const generateHtmlWebpackPluginInfoChunks = entry => {
  switch (entry) {
    case "changelog":
    case "500":
      return [];
    default:
      return ["main"];
  }
};

const generateHtmlWebpackPluginInfo = (argv, entry) => ({
  inject: true,
  scriptLoading: "defer",
  template: `./dist/${entry}.html`,
  filename: path.resolve(__dirname, '..', 'static', 'content', `${entry}.html`),
  chunks: generateHtmlWebpackPluginInfoChunks(entry),
});

module.exports = (env, argv) => ({
  entry: "./src/entry.js",
  output: {
    filename: '[name].[contenthash].js',
    path: path.resolve(__dirname, '..', 'static', 'content', 'static'),
    pathinfo: false,
    publicPath: '/static/'
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
        VERSION,
        CHANGELOG,
      },
      helpers: {
        projectHelpers: path.join(process.cwd(), "helpers", "*.js")
      },
      partials: [
        path.join(process.cwd(), "partials", "*.hbs"),
      ]
    }),
    ...pages
      .map(page => new HtmlWebpackPlugin(generateHtmlWebpackPluginInfo(argv, page))),
    // TODO: extra HtmlWebpackPlugin for 404 placed into templates directory
    new MiniCssExtractPlugin({
      filename: '[name].[contenthash].css'
    }),
  ]
});
