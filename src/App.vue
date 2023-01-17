<template>
    <!--
    SPDX-FileCopyrightText: Vũ Xuân Bình <binh9aqktk@gmail.com>
    SPDX-License-Identifier: AGPL-3.0-or-later
    -->
	<div id="content" class="app-musicnc bg-[#e9e9e9] dark:bg-[#888] px-[12%] py-5 min-h-screen grid justify-center items-center">
			<div
				class="main-conntainer border-1 border-solid border-[#d9d9d9] shadow-lg shadow-gray-300 dark:shadow-gray-600 dark:border-none grid grid-cols-4 rounded-3xl">
				<!-- <CLeft class="col-span-1 bg-white" /> -->
				<!-- <RouterView class="col-span-3 bg-[#E7E8F3]" /> -->
			</div>
			<div class="text-center text-xs text-gray-400 dark:text-gray-600">
				<p>
					Copyright 2022-PRESENT <sup>&copy;</sup>
					<a href="https://github.com/thuongtruong1009/bee-music">thuongtruong1009</a>.
					<a href="https://github.com/thuongtruong1009/bee-music/blob/main/LICENSE">All rights reserved</a>!
				</p>
			</div>
		<!-- <AppNavigation>
			<AppNavigationNew v-if="!loading"
				:text="t('musicnc', 'New note')"
				:disabled="false"
				button-id="new-musicnc-button"
				button-class="icon-add"
				@click="newNote" />
			<ul>
				<AppNavigationItem v-for="note in notes"
					:key="note.id"
					:title="note.title ? note.title : t('musicnc', 'New note')"
					:class="{active: currentNoteId === note.id}"
					@click="openNote(note)">
					<template slot="actions">
						<ActionButton v-if="note.id === -1"
							icon="icon-close"
							@click="cancelNewNote(note)">
							{{
							t('musicnc', 'Cancel note creation') }}
						</ActionButton>
						<ActionButton v-else
							icon="icon-delete"
							@click="deleteNote(note)">
							{{
							 t('musicnc', 'Delete note') }}
						</ActionButton>
					</template>
				</AppNavigationItem>
			</ul>
		</AppNavigation>
		<AppContent>
			<div v-if="currentNote">
				<input ref="title"
					v-model="currentNote.title"
					type="text"
					:disabled="updating">
				<textarea ref="content" v-model="currentNote.content" :disabled="updating" />
				<input type="button"
					class="primary"
					:value="t('musicnc', 'Save')"
					:disabled="updating || !savePossible"
					@click="saveNote">
			</div>
			<div v-else id="emptycontent">
				<div class="icon-file" />
				<h2>{{
				 t('musicnc', 'Create a note to get started') }}</h2>
			</div>
		</AppContent> -->
	</div>
</template>
<style>
@import "./assets/base.css";
@import "./assets/_variables.css";
</style>