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
        $url = "http://ac.mp3.zing.vn/complete";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $queryParams = http_build_query(
            array(
                'num' => 500,
                'query' => $name,
                'type' => 'artist,song,key,code'
            )
        );
        curl_setopt($ch, CURLOPT_POSTFIELDS, $queryParams);
        $data = curl_exec($ch);
        curl_close($ch);
        // Check for cURL errors
        if ($data === false) {
            $error = curl_error($ch);
            // Handle the error, return an appropriate response, or log the error
        }
        $params = [
            'data' => $data,
            'error' => $error,
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