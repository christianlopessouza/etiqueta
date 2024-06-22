<?php

use Src\Assets\Router;
use Src\Server\Controllers\ZebraController;

Router::post('/api/render', [ZebraController::class, 'render']);
Router::get('/api/render', [ZebraController::class, 'render']);
