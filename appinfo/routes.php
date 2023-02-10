<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Vũ Xuân Bình <binh9aqktk@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

/**
 * Create your routes in here. The name is the lowercase name of the controller
 * without the controller part, the stuff after the hash is the method.
 * e.g. page#index -> OCA\MusicNC\Controller\PageController->index()
 *
 * The controller class has to be registered in the application.php file since
 * it's instantiated in there
 */
return [
	'resources' => [
		'note' => ['url' => '/notes'],
		'note_api' => ['url' => '/api/0.1/notes']
	],
	'routes' => [
		['name' => 'page#index', 'url' => '/', 'verb' => 'GET'],
		['name' => 'about#index', 'url' => '/#/about', 'verb' => 'GET'],
		['name' => 'note_api#preflighted_cors', 'url' => '/api/0.1/{path}',
			'verb' => 'OPTIONS', 'requirements' => ['path' => '.+']],
		['name' => 'api#scan',	'url' => '/api/0.1/scan','verb' => 'GET'],
	]
];
