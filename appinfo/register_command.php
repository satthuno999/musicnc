<?php

/**
 * ownCloud - Music app
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Leizh <leizh@free.fr>
 * @author Morris Jobke <hey@morrisjobke.de>
 * @author Pauli Järvinen <pauli.jarvinen@gmail.com>
 * @copyright Leizh 2014
 * @copyright Morris Jobke 2014
 * @copyright Pauli Järvinen 2017 - 2022
 */

use OCA\MusicNC\App\Music;

$app = \OC::$server->query(Music::class);
$c = $app->getContainer();

$application->add(new OCA\MusicNC\Command\Scan(
		$c->query('UserManager'),
		$c->query('GroupManager'),
		$c->query('Scanner')
));
$application->add(new OCA\MusicNC\Command\ResetDatabase(
		$c->query('UserManager'),
		$c->query('GroupManager'),
		$c->query('Maintenance')
));
$application->add(new OCA\MusicNC\Command\ResetCache(
		$c->query('UserManager'),
		$c->query('GroupManager'),
		$c->query('DbCache')
));
$application->add(new OCA\MusicNC\Command\Cleanup(
		$c->query('Maintenance')
));
$application->add(new OCA\MusicNC\Command\RegisterMimeTypes(
		$c->query('MimeTypeLoader')
));
$application->add(new OCA\MusicNC\Command\PodcastAdd(
		$c->query('UserManager'),
		$c->query('GroupManager'),
		$c->query('PodcastChannelBusinessLayer'),
		$c->query('PodcastEpisodeBusinessLayer')
));
$application->add(new OCA\MusicNC\Command\PodcastReset(
		$c->query('UserManager'),
		$c->query('GroupManager'),
		$c->query('PodcastService')
));
$application->add(new OCA\MusicNC\Command\PodcastUpdate(
		$c->query('UserManager'),
		$c->query('GroupManager'),
		$c->query('PodcastService')
));
$application->add(new OCA\MusicNC\Command\PlaylistExport(
		$c->query('UserManager'),
		$c->query('GroupManager'),
		$c->query('RootFolder'),
		$c->query('PlaylistBusinessLayer'),
		$c->query('PlaylistFileService')
));
$application->add(new OCA\MusicNC\Command\PlaylistImport(
		$c->query('UserManager'),
		$c->query('GroupManager'),
		$c->query('RootFolder'),
		$c->query('PlaylistBusinessLayer'),
		$c->query('PlaylistFileService')
));
