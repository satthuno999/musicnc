<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: Vũ Xuân Bình <binh9aqktk@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\MusicNC\Controller;

use OCA\MusicNC\AppInfo\Application;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;
use OCP\Util;

class AboutController extends Controller {
	public function __construct(IRequest $request) {
		parent::__construct(Application::APP_ID, $request);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
	public function index(): TemplateResponse {
        $params = [ 'message' => 'Hello Nextcloud App!' ];

		return new TemplateResponse(Application::APP_ID, 'main',params, 'blank');
	}
}