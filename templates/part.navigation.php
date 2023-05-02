<?php
/**
 * MUSIC KMA
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the LICENSE.md file.
 *
 * @author S P A R K <binh9aqktk@gmail.com>
 * @copyright 2022-2023 S P A R K
 */
?>
<div class="view-nav">
	<div id="view-toggle" class="icon-toggle-<?php p($_['musicnc_view']); ?>">
		<?php $_['musicnc_view'] === 'pictures' ? p($l->t('Album Covers')) : p($l->t('List View')); ?>
	</div>
	<div>
		<input type="checkbox" class="checkbox" id="theme-toggle">
		<label for="checkbox" class="theme-togglelabel">
			<i class="fas fa-moon"></i>
			<i class="fas fa-sun"></i>
			<span class="ball"></span>
		</label>
	</div>
</div>
<div id="category_area">
	<select id="category_selector">
		<option value="" selected>
			<?php p($l->t('Selection')); ?>
		</option>
		<option value="Playlist">
			<?php p($l->t('Playlists')); ?>
		</option>
		<option value="Album">
			<?php p($l->t('Albums')); ?>
		</option>
		<option value="Album Artist">
			<?php p($l->t('Album Artists')); ?>
		</option>
		<option value="Artist">
			<?php p($l->t('Artists')); ?>
		</option>
		<option value="Folder">
			<?php p($l->t('Folders')); ?>
		</option>
		<option value="Genre">
			<?php p($l->t('Genres')); ?>
		</option>
		<option value="Title">
			<?php p($l->t('Titles')); ?>
		</option>
		<option value="Tags">
			<?php p($l->t('Tags')); ?>
		</option>
		<option value="Year">
			<?php p($l->t('Years')); ?>
		</option>
		<option value="LIKE">Yêu thích</option>
	</select>
	<button class="icon-add hidden" id="addPlaylist"></button>
</div>

<ul id="myCategory">
</ul>
<div>Radio</div>
<div>Poscast</div>
<div>Video</div>
<!--my playlist clone -->
<li class="plclone" id="pl-clone" data-pl="">
	<div id="playlist_controls">
		<input type="text" name="playlist" id="playlist" value="" />
		<button class="icon-checkmark"></button>
		<button class="icon-close"></button>
	</div>
</li>
<!--my playlist clone -->
<div class="ap_hidden" id="newPlaylist">
	<div id="newPlaylist_controls">
		<input type="text" name="newPlaylistTxt" id="newPlaylistTxt"
			placeholder="<?php p($l->t('Create new playlist')); ?>" />
		<button class="icon-checkmark" id="newPlaylistBtn_ok"></button>
		<button class="icon-close" id="newPlaylistBtn_cancel"></button>
	</div>
</div>