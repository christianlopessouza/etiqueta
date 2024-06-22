<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// exibir erros
ini_set('display_errors', 0);
// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

require __DIR__ . '/../vendor/_autoload.php';

use Src\Assets\Router;

http_response_code(200);

// return current URI 
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// include API and Render routes
// client routes manage the render of the application
Router::load(__DIR__ . '/../src/Client/Routes');

// server route manage the API of the application (/api)
Router::load(__DIR__ . '/../src/Server/Routes');

// execute request handler to render ou return a API JSON, comparing the current URI
Router::handleRequest($_SERVER['REQUEST_METHOD'], $uri);
