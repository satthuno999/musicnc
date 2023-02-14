/**
 * SPDX-FileCopyrightText: 2018 John Molakvoæ <skjnldsv@protonmail.com>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import { generateFilePath } from "@nextcloud/router";

import Vue from "vue";
// eslint-disable-next-line import/extensions
import App from "./App";
import { router } from "./routes";

// eslint-disable-next-line
__webpack_public_path__ = generateFilePath(appName, "", "js/");

Vue.mixin({ methods: { t, n } });
Vue.use(router);

export default new Vue({
  el: "#content",
  render: (h) => h(App),
});
