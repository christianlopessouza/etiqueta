<?php

namespace Src\Client\Controllers;
use Src\Client\Controllers\WebController;

class PanelController extends WebController
{
    public function home($request)
    {
         echo self::getContent(__DIR__ . '/../Views/home.php');
    }
}