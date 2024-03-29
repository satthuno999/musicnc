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
<div id="playlist-container" data-playlist="">
	<span id="individual-playlist-info"></span>
	<span id="individual-playlist-header">
		<span class="header-indi">
			<span class="header-num">1</span>
			<span class="header-title" style="cursor: pointer;">
				<?php p($l->t('Title')); ?>
			</span>
			<span class="header-artist" style="cursor: pointer;">
				<?php p($l->t('Artist')); ?>
			</span>
			<span class="header-album" style="cursor: pointer;">
				<?php p($l->t('Album')); ?>
			</span>
			<span class="header-time">
				<?php p($l->t('Length')); ?>
			</span>
			<span class="header-opt">&nbsp;</span>
		</span>
	</span>
	<br style="clear:both;" />
	<ul id="individual-playlist" class="albumwrapper"></ul>
</div>
<div id="partial-wrapper">
</div>
<div id="partial-wrapper-video">
	<?php print_unescaped($this->inc('part.video')); ?>
</div>
<div id="empty-container">
</div>