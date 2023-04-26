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

<div id="app-settings">
    <div id="app-settings-header">
        <button name="app settings"
                class="settings-button"
                data-apps-slide-toggle="#app-settings-content">
            <?php p($l->t('Settings')); ?>
        </button>
    </div>

    <div id="app-settings-content">
        <ul id="audio-settings">
            <li class="audio-settings-item icon-search">
                <a href="#" title="<?php p($l->t('Scan for audio files')); ?>" id="scanAudios" style="padding: 0 20px;">
                    <?php p($l->t('Scan for audio files')); ?>
                </a>
            </li>
            <li class="audio-settings-item icon-delete">
                <a href="#" title="<?php p($l->t('Reset library')); ?>" id="resetAudios" style="padding: 0 20px;">
                    <?php p($l->t('Reset library')); ?>
                </a>
            </li>
            <li class="audio-settings-item">
                <input class="checkbox" type="checkbox" id="sonos" <?php p($_['musicnc_sonos']) ?>/>
                <label for="sonos">&nbsp;<?php p($l->t('SONOS Playback')); ?></label>
            </li>
            <li class="audio-settings-item icon-settings">
                <a href="#" style="padding: 0 20px;" id="audioplayerSettings">
                    <?php p($l->t('Advanced Settings')); ?>
                </a>
            </li>
            <li class="audio-settings-item icon-external">
                <a href="https://github.com/satthuno999/musicnc/wiki/donate" target="_blank" style="padding: 0 20px;">
                    <?php p($l->t('Do you like this app?')); ?>
                </a>
            </li>
            <li class="audio-settings-item icon-info">
                <a href="https://github.com/satthuno999/musicnc/wiki" target="_blank" style="padding: 0 20px;">
                    <?php p($l->t('More information …')); ?>
                </a>
            </li>
        </ul>
    </div>
</div>
