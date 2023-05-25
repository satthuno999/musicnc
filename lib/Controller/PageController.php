<?php
/**
 * MUSIC KMA
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the LICENSE.md file.
 *
 * @author S P A R K <binh9aqktk@gmail.com>
 * @copyright 2022-2023 S P A R K
 * 
 */

namespace OCA\musicnc\Controller;

use OCA\musicnc\Event\LoadAdditionalScriptsEvent;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\IConfig;
use OCP\IL10N;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\EventDispatcher\IEventDispatcher;

/**
 * Controller class for main page.
 */
class PageController extends Controller
{

    private $userId;
    private $l10n;
    private $configManager;
    /** @var IEventDispatcher */
    protected $eventDispatcher;


    public function __construct(
        $appName,
        IRequest $request,
        $userId,
        IConfig $configManager,
        IEventDispatcher $eventDispatcher,
        IL10N $l10n
    )
    {
        parent::__construct($appName, $request);
        $this->appName = $appName;
        $this->userId = $userId;
        $this->configManager = $configManager;
        $this->l10n = $l10n;
        $this->eventDispatcher = $eventDispatcher;
    }

    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     * @throws \OCP\PreConditionNotMetException
     */
    public function index()
    {

        if ($this->configManager->getAppValue('musicnc_sonos', 'enabled') === "yes" AND $this->configManager->getAppValue('musicnc_sonos', 'sonos') === "checked") {
            $musicnc_sonos = $this->configManager->getUserValue($this->userId, 'musicnc_sonos', 'sonos') ?: false;
        } else {
            $musicnc_sonos = false;
        }

        $event = new LoadAdditionalScriptsEvent();
        $this->eventDispatcher->dispatchTyped($event);

        $response = new TemplateResponse('musicnc', 'index');
        $csp = new ContentSecurityPolicy();
        $csp->addAllowedMediaDomain('*'); //required for external m3u playlists
        $csp->addAllowedScriptDomain("'unsafe-inline'");
        $response->setContentSecurityPolicy($csp);
        $response->setParams([
            'musicnc_navigationShown' => $this->configManager->getUserValue($this->userId, $this->appName, 'navigation'),
            'musicnc_view' => $this->configManager->getUserValue($this->userId, $this->appName, 'view') ?: 'pictures',
            'musicnc_volume' => $this->configManager->getUserValue($this->userId, $this->appName, 'volume') ?: '1',
            'musicnc_repeat' => $this->configManager->getUserValue($this->userId, $this->appName, 'repeat') ?: 'none',
            'musicnc_sonos' => $musicnc_sonos,
        ]);
        return $response;
    }
}
