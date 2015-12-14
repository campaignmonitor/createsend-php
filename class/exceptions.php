<?php

class CurlException extends \RuntimeException
{
    public function __construct($curlMessage, $errorCode)
    {
        parent::__construct('Error making request with curl_error: ' . $curlMessage, $errorCode);
    }
}
