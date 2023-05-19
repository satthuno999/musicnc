<?php

namespace OCA\musicnc\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;

class RadioApiController extends Controller
{
    public function __construct($appName, IRequest $request)
    {
        parent::__construct($appName, $request);
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

        return new TemplateResponse('musicnc', 'partials/radioview', $params);
    }
}