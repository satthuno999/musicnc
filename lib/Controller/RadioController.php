<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: SPARK <binh9aqktk@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\musicnc\Controller;

use OCA\musicnc\AppInfo\Application;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;

class RadioController extends Controller
{
    public function __construct(IRequest $request)
    {
        parent::__construct(Application::APP_ID, $request);
    }
    /**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
    public function index()
    {
        return new TemplateResponse('musicnc', 'radioview');
    }
    /**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 */
    public function getAllByApi($page = 1,$limit=100,$hidebroken=true,$order="clickcount"){
        $url = "http://de1.api.radio-browser.info/json/stations";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $qrStr = "offset=".$page."&limit=".$limit."&hidebroken=".$hidebroken."&order=".$order;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $qrStr);
        $dataServer = curl_exec($ch);
        curl_close($ch);


        $params = [
            'data' => $dataServer,
        ];

        $response = new TemplateResponse('musicnc', 'partials/radioview', $params);
		return $response;
    }
}