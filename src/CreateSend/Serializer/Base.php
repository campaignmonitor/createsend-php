<?php

namespace CreateSend\Serializer;

use CreateSend\Log\LogInterface;

abstract class Base
{
    /**
     * @var LogInterface
     */
    private $_log;

    /**
     * @param LogInterface $log
     */
    public function __construct(LogInterface $log)
    {
        $this->_log = $log;
    }

    /**
     * Recursively ensures that all data values are utf-8 encoded.
     * @param array $data All values of this array are checked for utf-8 encoding.
     * @return array
     */
    public function check_encoding($data)
    {
        foreach ($data as $k => $v) {
            // If the element is a sub-array then recusively encode the array
            if (is_array($v)) {
                $data[$k] = $this->check_encoding($v);
                // Otherwise if the element is a string then we need to check the encoding
            } elseif (is_string($v)) {
                if ((function_exists('mb_detect_encoding') && mb_detect_encoding($v) !== 'UTF-8') ||
                    (function_exists('mb_check_encoding') && !mb_check_encoding($v, 'UTF-8'))
                ) {
                    // The string is using some other encoding, make sure we utf-8 encode
                    $v = utf8_encode($v);
                }

                $data[$k] = $v;
            }
        }

        return $data;
    }
}
