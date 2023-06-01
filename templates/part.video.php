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
<div class="playerVideo">
    <video id="html5Video" preload="auto">
    </video>

    <div id="current-video"></div>
</div>
<span id="individual-playlist-info-video">Video | Nhạc hình</span>
<span id="individual-playlist-header-video">
    <span class="header-indi-video">
        <span class="header-num-video">1</span>
        <span class="header-title-video" style="cursor: pointer;">
            <?php p($l->t('Title')); ?>
        </span>
        <span class="header-artist-video" style="cursor: pointer;">
            <?php p($l->t('Artist')); ?>
        </span>
        <span class="header-album-video" style="cursor: pointer;">
            <?php p($l->t('Album')); ?>
        </span>
        <span class="header-time-video">
            <?php p($l->t('Length')); ?>
        </span>
        <span class="header-opt-video">&nbsp;</span>
    </span>
</span>
<br style="clear:both;" />
<ul id="individual-playlist-video" class="albumwrapper-video"></ul>