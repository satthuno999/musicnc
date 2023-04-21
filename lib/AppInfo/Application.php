<?php
/**
 * Audioplayer
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the LICENSE.md file.
 *
 * @author S P A R K <audioplayer@scherello.de>
 * @copyright 2020 S P A R K
 */

namespace OCA\musicnc\AppInfo;

use OCA\musicnc\Dashboard\Widget;
use OCA\musicnc\Listener\LoadAdditionalScripts;
use OCA\musicnc\Search\Provider;
use OCA\Files_Sharing\Event\BeforeTemplateRenderedEvent;
use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\Util;

class Application extends App implements IBootstrap
{
    public const APP_ID = 'musicnc';

    public function __construct(array $urlParams = [])
    {
        parent::__construct(self::APP_ID, $urlParams);
    }

    public function register(IRegistrationContext $context): void
    {
        $context->registerDashboardWidget(Widget::class);
        $context->registerEventListener(BeforeTemplateRenderedEvent::class, LoadAdditionalScripts::class);
        $context->registerSearchProvider(Provider::class);
        $this->registerFileHooks();
        $this->registerUserHooks();
    }

    protected function registerFileHooks()
    {
        Util::connectHook(
            'OC_Filesystem', 'delete', '\OCA\musicnc\Hooks\FileHooks', 'deleteTrack'
        );
    }

    protected function registerUserHooks()
    {
        Util::connectHook(
            'OC_User', 'post_deleteUser', '\OCA\musicnc\Hooks\UserHooks', 'deleteUser'
        );
    }

    public function boot(IBootContext $context): void
    {
    }

}