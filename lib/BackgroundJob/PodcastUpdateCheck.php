<?php declare(strict_types=1);

/**
 * ownCloud - Music app
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Pauli Järvinen <pauli.jarvinen@gmail.com>
 * @copyright Pauli Järvinen 2021 - 2023
 */

namespace OCA\MusicNC\BackgroundJob;

use OCA\MusicNC\App\Music;
use OCA\MusicNC\Utility\PodcastService;

use OC\BackgroundJob\TimedJob;
// NC15+ would have TimedJob also as a public class and has deprecated the private class used above.
// However, we can't use this new alternative as it's not available on ownCloud.
// use OCP\BackgroundJob\TimedJob;

class PodcastUpdateCheck extends TimedJob {

	/**
	 * Check podcast updates on the background
	 */
	public function run($arguments) {
		$app = \OC::$server->query(musicnc::class);

		$container = $app->getContainer();

		$logger = $container->query('Logger');
		$logger->log('Run ' . \get_class(), 'debug');

		$minInterval = (float)$container->query('Config')->getSystemValue('musicnc.podcast_auto_update_interval', 24); // hours
		// negative interval values can be used to disable the auto-update
		if ($minInterval >= 0) {
			$users = $container->query('PodcastChannelBusinessLayer')->findAllUsers();
			$podcastService = $container->query('PodcastService');
			$channelsChecked = 0;

			foreach ($users as $userId) {
				$podcastService->updateAllChannels($userId, $minInterval, false, function (array $channelResult) use ($logger, $userId, &$channelsChecked) {
					$id = (isset($channelResult['channel'])) ? $channelResult['channel']->getId() : -1;

					if ($channelResult['updated']) {
						$logger->log("Channel $id of user $userId was updated", 'debug');
					} elseif ($channelResult['status'] === PodcastService::STATUS_OK) {
						$logger->log("Channel $id of user $userId had no changes", 'debug');
					} else {
						$logger->log("Channel $id of user $userId update failed", 'debug');
					}

					$channelsChecked++;
				});
			}

			if ($channelsChecked === 0) {
				$logger->log('No podcast channels were due to check for updates', 'debug');
			} else {
				$logger->log("$channelsChecked podcast channels in total were checked for updates", 'debug');
			}
		}
		else {
			$logger->log('Automatic podcast updating is disabled via config.php', 'debug');
		}

	}
}
