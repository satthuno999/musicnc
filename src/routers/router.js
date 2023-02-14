import { createRouter, createWebHashHistory } from 'vue-router'
import About from '../views/About.vue'
import Home from '../views/Home.vue'

const routes = [
	{ path: '/', component: Home },
	{ path: '/about', component: About },
]
const router = createRouter({
	history: createWebHashHistory(),
	routes,
})

export default router
