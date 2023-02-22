<?php

namespace OCA\musicnc\Controller;

use OCP\AppFramework\ApiController;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class UtilApiController extends ApiController {
	public function __construct($AppName, IRequest $request) {
		parent::__construct($AppName, $request);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 * @CORS
	 * @return JSONResponse
	 */
	public function getApiVersion(): JSONResponse {
		$response = [
			'musicnc_version' => [0, 10, 1], /* VERSION_TAG do not change this line manually */
			'api_version' => [
				'epoch' => 0,
				'major' => 1,
				'minor' => 0
			]
		];
		return new JSONResponse($response, 200);
	}
}
