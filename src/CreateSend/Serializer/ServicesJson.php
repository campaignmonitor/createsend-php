<?php

namespace CreateSend\Serializer;

use CreateSend\Log\LogInterface;
use Services_JSON;

if (!function_exists("CS_REST_SERIALISATION_get_available")) {
    function CS_REST_SERIALISATION_get_available(LogInterface $log)
    {
        $log->log_message('Getting serialiser', __FUNCTION__, LogInterface::LEVEL_VERBOSE);
        if (function_exists('json_decode') && function_exists('json_encode')) {
            return new NativeJson($log);
        } else {
            return new ServicesJson($log);
        }
    }
}

class ServicesJson extends Base implements SerializerInterface
{
    /**
     * @var Services_JSON
     */
    private $_serialiser;

    public function __construct(LogInterface $log)
    {
        parent::__construct($log);

        $this->_serialiser = new Services_JSON();
    }

    public function get_content_type()
    {
        return 'application/json';
    }

    public function get_format()
    {
        return 'json';
    }

    public function get_type()
    {
        return 'services_json';
    }

    /**
     * {@inheritdoc}
     */
    public function serialise($data)
    {
        if (is_null($data) || $data == '') {
            return '';
        }

        return $this->_serialiser->encode($this->check_encoding($data));
    }

    /**
     * {@inheritdoc}
     */
    public function deserialise($text)
    {
        $data = $this->_serialiser->decode($text);

        return is_null($data) ? $text : $data;
    }
}
