<?php
declare(strict_types=1);
// SPDX-FileCopyrightText: SPARK <binh9aqktk@gmail.com>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\musicnc\Controller;

use OCA\musicnc\AppInfo\Application;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Http\ContentSecurityPolicy;
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
    * @param string $name
    */
    public function search(string $name = "khoi")
    {
        $url = "https://de1.api.radio-browser.info/json/stations/bylanguage/vietnamese";
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $dataLang = curl_exec($ch);
        curl_close($ch);

        $data = curl_exec($ch);
        // Check for cURL errors
        if ($data === false) {
            $error = curl_error($ch);
            // Handle the error, return an appropriate response, or log the error
        }
        curl_close($ch);
        $params = [
            'data' => $dataLang,
            'error' => $error,
        ];

        $response = new TemplateResponse('musicnc', 'partials/zingview', $params);
        // $csp = new ContentSecurityPolicy();
        // $csp->addAllowedMediaDomain('*');
        // $csp->addAllowedScriptDomain("unsafe-inline");
        // $csp->allowInlineScript(true);
        // $csp->allowInlineStyle(true);
        // $csp->allowEvalScript(true);
        // $response->setContentSecurityPolicy($csp);
        return $response;
    }
}