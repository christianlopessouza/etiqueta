<?php

namespace Src\Assets;

class Request
{
    public $params = [];
    public $body = [];
    public $query = [];
    public $files;
    public $headers;
}
