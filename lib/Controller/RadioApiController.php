<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: SPARK <binh9aqktk@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\musicnc\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;

class RadioApiController extends Controller
{
    private $userId;

    public function __construct($appName, IRequest $request,$userId)
    {
        parent::__construct($appName, $request);
        $this->userId = $userId;
    }

    public function index()
    {
        return new TemplateResponse('musicnc', 'radioview');
    }
    /**
     * @NoAdminRequired
     * @NoCSRFRequired
     */
    public function getAllByApi(){
        $url = "http://all.api.radio-browser.info/json/servers";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $data = curl_exec($ch);
        curl_close($ch);

        $params = [
            'data' => $data,
        ];

        $response = new TemplateResponse('musicnc', 'partials/radioview', $params);
		return $response;
    }
}