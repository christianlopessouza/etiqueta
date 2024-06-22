<?php

namespace Src\Assets;

class Router
{
    private static $routes = [];

    public static function get($path, $handler)
    {
        // add routes to the array of routes in GET request
        self::$routes[] = ['method' => 'GET', 'path' => $path, 'handler' => $handler];
    }

    public static function post($path,  $handler)
    {
        // add routes to the array of routes in POST request
        self::$routes[] = ['method' => 'POST', 'path' => $path, 'handler' => $handler];
    }

    public static function handleRequest($method, $uri)
    {
        // loop through list of routes
        foreach (self::$routes as $route) {
            // verify that the route matches - method (POST or GET) and uri
            if ($route['method'] === $method && self::routerVerifyer($route['path'], $uri)) {
                // extract query,route or body request params
                $request = self::extractParams($route['path'], $uri);
                // verify if route have middlewares to decide if will execute a loop or a single request
                $handler_method = self::handlerMethod($route['handler']);

                if ($handler_method === 'non-middleware') {
                    // execute single handler method
                    self::executeHandler($route['handler'], $request);
                } else if ($handler_method === 'with-middleware') {
                    // execute loop of handler methods
                    foreach ($route['handler'] as $handler) {
                        $requets = self::executeHandler($handler, $request);
                    }
                } else {
                    // return error 403 in case of invalid handler method
                    echo '403 error. Wrong Method';
                    http_response_code(403);
                    exit();
                }
                exit();
            }
        }

        // Handle 404
        echo "404 Not Found";
        http_response_code(404);
        exit();
    }

    private static function routerVerifyer($pattern, $uri)
    {
        // input: /address/param

        // replace (/) per (\/): \/address\/{param}
        $pattern = str_replace('/', '\/', $pattern);

        // add delimiters between the pattern: /^\/address\/{param}$/
        $pattern = '/^' . $pattern . '$/';

        // tranform route-params into dymanic expression: /^\/address\/([^\/]+)$/
        $pattern = preg_replace('/\{([^\/]+)\}/', '([^\/]+)', $pattern);

        // compare the input content with the pattern
        return preg_match($pattern, $uri);
    }

    private static function extractParams($pattern, $uri)
    {
        // verify params
        if (empty($pattern) || empty($uri)) return null;

        // define params
        $request = new Request();

        // extract Route-params from the URI using regular expressions (ajax)
        $regex_pattern = preg_quote($pattern, '/');
        $regex_pattern = str_replace('\{', '{', $regex_pattern);
        $regex_pattern = str_replace('\}', '}', $regex_pattern);
        $regex_pattern = preg_replace('/\{([^\/]+)\}/', '(?P<$1>[^\/]+)', $regex_pattern);
        $regex_pattern = '/^' . str_replace('/', '\/', $regex_pattern) . '$/';

        // output example: /address/{id} <-> /address/123 => {id: 123}
        if (preg_match($regex_pattern, $uri, $matches)) {
            // extract route-params from the URI and add in $request[params] variable
            foreach ($matches as $key => $value) {
                if (!is_int($key)) {
                    $request->params[$key] = $value;
                }
            }
        }

        // extract query params
        $request->query = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);

        // extract body params from POST request
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $request->body = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
            if (!empty($_FILES))
                $request->files = $_FILES;
        }

        return $request;
    }


    private static function handlerMethod($handler)
    {
        // verify if handler is an array of 'handlers' of a callable handler. single method handler
        if (is_array($handler) && is_callable($handler)) return 'non-middleware';

        // if first element of array is an array, it means that the handler has middlewares
        if (is_array($handler) && is_array($handler[0])) return 'with-middleware';

        return null;
    }

    private static function executeHandler($route, $request)
    {
        $controller = $route[0];
        $method = $route[1];
        // Carrega o controlador e chama o método
        try {
            $controller = new $controller();
            $response = call_user_func([$controller, $method], $request);

            if ($response instanceof HandlerException) {
                $response = ['error_code' => $response->getErrorCode(), 'message' => $response->getMessage()];
            }

            if (is_array($response)) echo json_encode($response);

            $http_code = http_response_code();

            if (!($http_code >= 200 && $http_code < 300)) exit();
        } catch (\Exception $th) {
            var_dump($th);
            exit();
        }

        return $response;
    }

    public static function load($path)
    {
        $path = $path . '/*.php';
        // Inclui todos os arquivos de rotas
        foreach (glob($path) as $filename) {
            include_once $filename;
        }
    }
}
