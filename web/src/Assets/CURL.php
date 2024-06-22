<?php

namespace Src\Assets;

class CURL
{
    private $curl;
    public $response;

    public function __construct($method)
    {
        $this->curl = curl_init();
        curl_setopt($this->curl, CURLOPT_RETURNTRANSFER, true);
        if ($method === 'POST') curl_setopt($this->curl, CURLOPT_POST, true);
    }

    public function headers($header)
    {
        curl_setopt($this->curl, CURLOPT_HTTPHEADER, $header);
    }

    public function body($body)
    {
        curl_setopt($this->curl, CURLOPT_POSTFIELDS, $body);
    }
    public function endpoint($url)
    {
        curl_setopt($this->curl, CURLOPT_URL, $url);
    }

    public function send()
    {
        $this->response = curl_exec($this->curl);

        return $this->response;
    }

    public function close()
    {
        curl_close($this->curl);
    }

    public function getHttpCode()
    {
        return curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
    }
}
