<?php

namespace CreateSend\Transport;

use CreateSend\Exception\TransportException;
use CreateSend\Log\LogInterface;

class TransportFactory
{
    /**
     * @param bool $requires_ssl
     * @param LogInterface $log
     *
     * @return Curl|Socket
     *
     * @throws TransportException
     */
    public static function create($requires_ssl, LogInterface $log = null)
    {
        if (function_exists('curl_init') && function_exists('curl_exec')) {
            return new Curl($log);
        }

        if (static::can_use_raw_socket($requires_ssl)) {
            return new Socket($log);
        }

        $log->log_message('No transport is available', __FUNCTION__, LogInterface::LEVEL_ERROR);

        throw new TransportException('No transport is available.' .
            ($requires_ssl ? ' Try using non-secure (http) mode or ' : ' Please ') .
            'ensure the cURL extension is loaded');

    }

    /**
     * @param bool $requires_ssl
     *
     * @return bool
     */
    protected static function can_use_raw_socket($requires_ssl)
    {
        if (function_exists('fsockopen')) {
            if ($requires_ssl) {
                return extension_loaded('openssl');
            }

            return true;
        }

        return false;
    }
}
