import Vue from "vue";
import VueRouter from "vue-router";
import About from "./views/About.vue";
import Home from "./views/HelloWorld.vue";

Vue.use(VueRouter);
const router = new VueRouter({
  history: VueRouter.createWebHashHistory(),
  routes,
});

export default router
