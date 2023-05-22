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
    public function getAllByApi($page = 1, $limit = 100, $hidebroken = true, $order = "clickcount")
    {
        $url = "http://de1.api.radio-browser.info/json/stations";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $queryParams = http_build_query(
            array(
                'offset' => $page,
                'limit' => $limit,
                'hidebroken' => $hidebroken,
                'order' => $order
            )
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $queryParams);
        $dataServer = curl_exec($ch);
        curl_close($ch);

        $urlLang = "http://de1.api.radio-browser.info/json/languages";
        $chLang = curl_init($urlLang);
        curl_setopt($chLang, CURLOPT_RETURNTRANSFER, true);
        $queryParamsLang = http_build_query(
            array(
                'order' => 'name'
            )
        );
        curl_setopt($chLang, CURLOPT_POSTFIELDS, $queryParamsLang);
        $dataCountrys = curl_exec($chLang);
        curl_close($chLang);

        $params = [
            'data' => $dataServer,
            'dataCountrys' => $dataCountrys
        ];

        $response = new TemplateResponse('musicnc', 'partials/radioview', $params);
        return $response;
    }
}