// SPDX-FileCopyrightText: Vũ Xuân Bình <binh9aqktk@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later
const webpackConfig = require('@nextcloud/webpack-vue-config')
const { VueLoaderPlugin } = require("vue-loader");

module.exports = {
  module: {
    rules: [
      // ... other rules
      {
        test: /\.vue$/,
        loader: "vue-loader",
      },
    ],
  },
  plugins: [
    // make sure to include the plugin!
    webpackConfig,
    new VueLoaderPlugin(),
  ],
};
