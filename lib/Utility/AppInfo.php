<?php declare(strict_types=1);

/**
 * ownCloud - Music app
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Pauli Järvinen <pauli.jarvinen@gmail.com>
 * @copyright Pauli Järvinen 2022
 */

namespace OCA\MusicNC\Utility;

class AppInfo {

	public const APP_ID = 'Musicnc';

	public static function getVersion() {
		// Nextcloud 14 introduced a new API for this which is not available on ownCloud.
		// Nextcloud 25 removed the old API for good.
		$appManager = \OC::$server->getAppManager();
		if (\method_exists($appManager, 'getAppVersion')) {
			return $appManager->getAppVersion(self::APP_ID); // NC14+
		} else {
			return \OCP\App::getAppVersion(self::APP_ID); // OC or NC13
		}
	}
}