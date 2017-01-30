<?php

namespace CreateSend\Wrapper;

/**
 * A general result object returned from all Campaign Monitor API calls.
 * @author tobyb
 *
 */
class Result
{
    /**
     * The deserialised result of the API call
     * @var mixed
     */
    public $response;

    /**
     * The http status code of the API call
     * @var int
     */
    public $http_status_code;

    /**
     * @param mixed $response
     * @param int $code
     */
    public function __construct($response, $code)
    {
        $this->response = $response;
        $this->http_status_code = $code;
    }

    /**
     * Can be used to check if a call to the api resulted in a successful response.
     * @return boolean False if the call failed. Check the response property for the failure reason.
     */
    public function was_successful()
    {
        return $this->http_status_code >= 200 && $this->http_status_code < 300;
    }
}
