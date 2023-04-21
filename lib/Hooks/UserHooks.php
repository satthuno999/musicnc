<?php
/**
 * MUSIC KMA
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the LICENSE.md file.
 *
 * @author S P A R K <binh9aqktk@gmail.com>
 * @copyright 2012-2023 S P A R K
 */
 
namespace OCA\musicnc\Hooks;
use OCA\musicnc\Controller;

class UserHooks {
	public static function deleteUser($params) {
		$userId = $params['uid'];
		$app = new \OCA\musicnc\AppInfo\Application();
        	$container = $app->getContainer();
        $container->query(\OCA\musicnc\Controller\DbController::class)->resetMediaLibrary($userId, null, true);
	}    
}
