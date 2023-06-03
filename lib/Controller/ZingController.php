<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: SPARK <binh9aqktk@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\musicnc\Controller;

use OCA\musicnc\AppInfo\Application;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\AppFramework\Http\Template\PublicTemplateResponse;

use OCP\IRequest;

class ZingController extends Controller
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
     * @PublicPage
     * @CORS
     * @param string $name
     */
    public function searchName(string $name = "khoi")
    {
        $curl = curl_init();
        $url  = "https://shazam.p.rapidapi.com/search?term=".urlencode($name)."&locale=en-US&offset=0&limit=5";
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "X-RapidAPI-Host: shazam.p.rapidapi.com",
                "X-RapidAPI-Key: 761f5d2787mshd78663331979465p1ecfafjsn46bc7cb565a2"
            ],
        ]);

        $data = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);
        $params = [
            'data' => $data,
            'error' => $err,
            'name' => $name,
        ];

        $response = new TemplateResponse('musicnc', 'partials/zingview', $params);
        $csp = new ContentSecurityPolicy();
        $csp->addAllowedImageDomain('*');
        $csp->addAllowedMediaDomain('*');
        $response->setContentSecurityPolicy($csp);
        return $response;
    }
}