import VueRouter from 'vue-router'
import About from '../views/About.vue'
import Home from '../views/Home.vue'

const routes = [
	{ path: '/', component: Home },
	{ path: '/about', component: About },
]
const router = new VueRouter({
  routes,
});

export default router
