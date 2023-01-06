/**
 * SPDX-FileCopyrightText: 2018 John Molakvoæ <skjnldsv@protonmail.com>
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import { generateFilePath } from '@nextcloud/router'

import Vue from 'vue'
import { createApp } from 'vue'
import App from './App.vue'
import { createPinia } from 'pinia'

// eslint-disable-next-line
__webpack_public_path__ = generateFilePath(appName, "", "js/");

Vue.mixin({ methods: { t, n } })

const app = createApp(App)
app.use(createPinia())
app.mount("#content");
// export default new Vue({
// 	el: '#content',
// 	render: (h) => h(App),
// })
