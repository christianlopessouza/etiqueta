<?

namespace Src\Client\Controllers;

class WebController
{
    public static function getContent($dir)
    {
        ob_start(); // Inicia o buffer de saída
        require_once $dir;
        return ob_get_clean(); // Captura o conteúdo do buffer e limpa o buffer
    }
}
