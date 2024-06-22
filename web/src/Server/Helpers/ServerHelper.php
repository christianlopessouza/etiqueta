<?

namespace Src\Server\Helpers;

class ServerHelper
{
    public static function hostname()
    {
        $http_procol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' || $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http';
        return sprintf('%s://%s', $http_procol, $_SERVER['HTTP_HOST']);
    }
}
