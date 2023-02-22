/**
 * Nextcloud  app
 * Vue router module
 * ----------------------
 * @license AGPL3 or later
 */
import Vue from "vue"
import VueRouter from "vue-router"

import Index from "../components/AppIndex.vue"
import NotFound from "../components/NotFound.vue"
import Search from "../components/SearchResults.vue"

Vue.use(VueRouter)

// The router will try to match routers in a descending order.
// Routes that share the same root, must be listed from the
//  most descriptive to the least descriptive, e.g.
//  /section/component/subcomponent/edit/:id
//  /section/component/subcomponent/new
//  /section/component/subcomponent/:id
//  /section/component/:id
//  /section/:id
const routes = [
    // Search routes
    {
        path: "/category/:value",
        name: "search-category",
        component: Search,
        props: { query: "cat" },
    },

    // Index is the last defined route
    { path: "/", name: "index", component: Index },

    // Anything not matched goes to NotFound
    { path: "*", name: "not-found", component: NotFound },
]

export default new VueRouter({
    routes,
})
