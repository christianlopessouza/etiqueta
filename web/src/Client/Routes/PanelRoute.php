<?php

use Src\Assets\Router;
use Src\Client\Controllers\PanelController;

Router::get('/', [PanelController::class, 'home']);
