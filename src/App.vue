<script setup lang="ts">
import CLeft from './components/CLeft.vue'
</script>

<template>
	<main class="bg-[#e9e9e9] dark:bg-[#888] px-[12%] py-5 min-h-screen grid justify-center items-center">
		<div class="main-conntainer border-1 border-solid border-[#d9d9d9] shadow-lg shadow-gray-300 dark:shadow-gray-600 dark:border-none grid grid-cols-4 rounded-3xl">
			<AppNavigation>
				<CLeft class="col-span-1 bg-white" />
			</AppNavigation>
			<AppContent>
				<div>CONTENT</div>>
			</AppContent>
		</div>
		<div class="text-center text-xs text-gray-400 dark:text-gray-600">
			<p>
				Copyright 2022-PRESENT <sup>&copy;</sup>
				<a href="https://github.com/thuongtruong1009/bee-music">thuongtruong1009</a>.
				<a href="https://github.com/thuongtruong1009/bee-music/blob/main/LICENSE">All rights reserved</a>!
			</p>
		</div>
	</main>
</template>

<script>
import AppContent from '@nextcloud/vue/dist/Components/AppContent'
import AppNavigation from '@nextcloud/vue/dist/Components/AppNavigation'
export default {
	name: 'App',
	components: {
		AppContent,
		AppNavigation,
	},
}
</script>
<style>
@import "./assets/base.css";
@import "./assets/_variables.css";
</style>
