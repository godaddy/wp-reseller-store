const webpack = require("webpack");
const path = require("path");
const autoprefixer = require("autoprefixer");
const MiniCssExtractPlugin = require("mini-css-extract-plugin");

const paths = {
  pluginBlocksJs: "./.dev/src/index.js",
  pluginDist: "./assets/js/editor.blocks.min",
};

// Source maps are resource heavy and can cause out of memory issue for large source files.
const shouldUseSourceMap = process.env.NODE_ENV === "dev";

// Export configuration.
module.exports = {
  mode: "production",
  target: ["web", "es5"],
  entry: {
    "./assets/js/editor.blocks.min": paths.pluginBlocksJs, // 'name' : 'path/file.ext'.
  },
  output: {
    // Add /* filename */ comments to generated require()s in the output.
    pathinfo: true,
    // The dist folder.
    path: path.resolve(__dirname),
    filename: "[name].js",
  },
  // You may want 'eval' instead if you prefer to see the compiled output in DevTools.
  devtool: shouldUseSourceMap ? "cheap-eval-source-map" : "source-map",
  plugins: [new MiniCssExtractPlugin()],
  module: {
    rules: [
      {
        test: /\.(js|jsx|mjs)$/,
        exclude: /(node_modules|bower_components)/,
        use: {
          loader: "babel-loader",
          options: {
            // This is a feature of `babel-loader` for webpack (not Babel itself).
            // It enables caching results in ./node_modules/.cache/babel-loader/
            // directory for faster rebuilds.
            cacheDirectory: true,
          },
        },
      },
      {
        test: /assets\/css\/*\.(sa|sc|c)ss$/,
        exclude: /(node_modules|bower_components)/,
        use: [
          {
            loader: MiniCssExtractPlugin.loader,
          },
          "raw-loader",
          {
            loader: "postcss-loader",
            options: {
              ident: "postcss",
              plugins: [
                autoprefixer({
                  overrideBrowserslist: [
                    ">1%",
                    "last 4 versions",
                    "Firefox ESR",
                    "not ie < 9", // React doesn't support IE8 anyway
                  ],
                  flexbox: "no-2009",
                }),
              ],
            },
          },
        ],
      },
      {
        test: /\.scss$/,
        exclude: /(node_modules|bower_components)/,
        use: [
					"style-loader",
					"css-loader",
          "sass-loader",
        ],
      },
    ],
  },
  stats: "minimal",
  // Add externals.
  externals: {
    react: "React",
    "react-dom": "ReactDOM",
    ga: "ga", // Old Google Analytics.
    gtag: "gtag", // New Google Analytics.
    jquery: "jQuery", // import $ from 'jquery' // Use the WordPress version.
  },
};
