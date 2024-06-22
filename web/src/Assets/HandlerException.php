<?php

namespace Src\Assets;

class HandlerException extends \Exception
{
    protected $error_code;
    public function __construct($error_code, $message)
    {
        $this->error_code = $error_code;
        parent::__construct($message);
    }
    public function getErrorCode()
    {
        return $this->error_code;
    }
}
