<?php

declare(strict_types=1);

/**
 * MUSIC KMA
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the LICENSE.md file.
 *
 * @author S P A R K <binh9aqktk@gmail.com>
 * @copyright 2020 S P A R K
 */

namespace OCA\musicnc\Listener;

use OCA\Files\Event\LoadAdditionalScriptsEvent;
use OCA\Files_Sharing\Event\BeforeTemplateRenderedEvent;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Util;

class LoadAdditionalScripts implements IEventListener
{
    public function handle(Event $event): void
    {
        if ($event instanceof BeforeTemplateRenderedEvent) {
            Util::addScript('musicnc', 'viewer/viewer');
            Util::addScript('musicnc', 'sharing/sharing');
            Util::addStyle('musicnc', '3rdparty/fontello/css/fontello');
        } else {
            return;
        }
    }
}