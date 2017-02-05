<?php

namespace CreateSend\Transport;

interface TransportInterface
{
    const CS_REST_GET = 'GET';
    const CS_REST_POST = 'POST';
    const CS_REST_PUT = 'PUT';
    const CS_REST_DELETE = 'DELETE';

    /**
     * @return string The type of transport used
     */
    public function get_type();

    /**
     * @param array $call_options
     * @return array
     */
    public function make_call($call_options);
}
