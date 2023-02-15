<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Vũ Xuân Bình <binh9aqktk@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\KMAMUSIC\AppInfo;

use OCP\AppFramework\App;

class Application extends App {
	public const APP_ID = 'kmamusic';

	public function __construct() {
		parent::__construct(self::APP_ID);
	}
}
