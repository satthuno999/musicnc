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

use OCP\Util;

Util::addStyle('musicnc', 'bar-ui');
Util::addStyle('musicnc', 'style');
Util::addStyle('files', 'detailsView');
Util::addStyle('musicnc', '3rdparty/fontello/css/fontello');
Util::addScript('musicnc', 'app');
Util::addScript('musicnc', 'sidebar');
Util::addScript('musicnc', 'settings/settings');
if ($_['musicnc_sonos'] !== 'checked') {
    Util::addScript('musicnc', 'player');
}

?>
<script src="https://cdn.tailwindcss.com"></script>
<input type="hidden" name="id" value="">
<input type="hidden" id="musicnc_volume" value="<?php p($_['musicnc_volume']); ?>">
<input type="hidden" id="musicnc_sonos" value="<?php p($_['musicnc_sonos']); ?>">
<input type="hidden" id="musicnc_repeat" value="<?php p($_['musicnc_repeat']); ?>">

<div id="app-navigation" class="shadow-gray-300 dark:shadow-gray-600 dark:border-none" <?php if ($_['musicnc_navigationShown'] === 'false')
    echo 'class="hidden"'; ?>>

    <?php print_unescaped($this->inc('part.navigation')); ?>

    <?php print_unescaped($this->inc('settings/part.settings')); ?>

</div>

<div id="app-content" class="shadow-gray-300 dark:shadow-gray-600 dark:border-none">
    <div id="loading">
        <i class="ioc-spinner ioc-spin"></i>
    </div>



    <div id="searchresults" class="hidden" data-appfilter="audioplayer"></div>

    <?php print_unescaped($this->inc('part.container')); ?>

</div>

<div id="app-sidebar" class="app-sidebar details-view scroll-container disappear" data-trackid="">
    <?php print_unescaped($this->inc('part.sidebar')); ?>
</div>
<div>
    <?php if ($_['musicnc_sonos'] !== 'checked')
        print_unescaped($this->inc('part.audio')); ?>
    <?php if ($_['musicnc_sonos'] === 'checked')
        print_unescaped($this->inc('part.sonos-bar')); ?>
</div>