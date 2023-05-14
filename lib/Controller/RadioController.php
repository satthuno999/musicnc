<?php

namespace OCA\musicnc\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\IRequest;

class MyController extends Controller
{
    public function __construct($appName, IRequest $request)
    {
        parent::__construct($appName, $request);
    }

    public function index()
    {
        return new TemplateResponse('musicnc', 'radio');
    }
}