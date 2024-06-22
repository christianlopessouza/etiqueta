<?php

namespace Src\Server\Controllers;

use Src\Assets\HandlerException;
use Src\Server\Services\ZebraService;

class ZebraController
{
    public static function render($request)
    {
        try {
            $file = $request->files['layout'];
     
            if (!!$file) {
                $body = $request->body;
                $width = $body['width'];
                $height = $body['height'];

                $response = ZebraService::reader($file['tmp_name'], $width, $height);
                if (!!$response) {
                    return [
                        'url' => $response['pdf'],
                        'images'=>[$response['img']]
                    ];
                }
                throw new HandlerException(1, 'Error to generate PDF');
            }
            throw new HandlerException(3, 'File not sended');
        } catch (HandlerException $error) {
            return $error;
        }
    }
}
