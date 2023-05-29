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
    public function search($name = "khoi")
    {
        $url = "    http://ac.mp3.zing.vn/complete?type=artist,song,key,code&num=500&query=".$name;
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $dataLang = curl_exec($ch);
        curl_close($ch);

        $params = [
            'data' => $dataLang,
        ];

        $response = new TemplateResponse('musicnc', 'partials/zingview', $params);
        $csp = new ContentSecurityPolicy();
        $csp->addAllowedMediaDomain('*');
        $csp->addAllowedScriptDomain("'unsafe-inline'");
        $response->setContentSecurityPolicy($csp);
        return $response;
    }
}