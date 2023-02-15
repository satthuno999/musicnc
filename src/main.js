/**
 * SPDX-FileCopyrightText: 2018 John Molakvoæ <skjnldsv@protonmail.com>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import { generateFilePath } from "@nextcloud/router";

import  Vue  from "vue";
import App from "./App";
import router from "./routers/router";

// eslint-disable-next-line
__webpack_public_path__ = generateFilePath(appName, "", "js/");

// Vue.mixin({ methods: { t, n } });

export default new Vue({
  router,
  // el: "#content",
  render: (h) => h(App)
}).$mount("#content");
