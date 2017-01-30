<?php

namespace CreateSend\Transport;

use CreateSend\Log\LogInterface;
use CreateSend\Exception\CurlException;

/**
 * Provide HTTP request functionality via cURL extensions
 *
 * @author tobyb
 * @since 1.0
 */
class Curl extends Base implements TransportInterface
{
    /**
     * @var bool
     */
    private $_curl_zlib;

    /**
     * @param LogInterface $log
     */
    public function __construct(LogInterface $log)
    {
        parent::__construct($log);

        $curl_version = curl_version();
        $this->_curl_zlib = isset($curl_version['libz_version']);
    }

    /**
     * {@inheritdoc}
     */
    public function get_type()
    {
        return 'cURL';
    }

    /**
     * {@inheritdoc}
     */
    public function make_call($call_options)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $call_options['route']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, true);
        $headers = array();
        $headers[] = 'Content-Type: ' . $call_options['contentType'];


        if (array_key_exists('authdetails', $call_options) &&
            isset($call_options['authdetails'])
        ) {
            if (array_key_exists('username', $call_options['authdetails']) &&
                array_key_exists('password', $call_options['authdetails'])
            ) {
                # Authenticating using basic auth for retrieving user's API key.
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                curl_setopt($ch, CURLOPT_USERPWD, $call_options['authdetails']['username'] . ':' . $call_options['authdetails']['password']);
            } elseif (array_key_exists('access_token', $call_options['authdetails'])) {
                # Authenticating using OAuth.
                $access_token = $call_options['authdetails']['access_token'];
                $headers[] = 'Authorization: Bearer ' . $access_token;
            } elseif (array_key_exists('api_key', $call_options['authdetails'])) {
                # Authenticating using an API key.
                curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
                $api_key = $call_options['authdetails']['api_key'];
                curl_setopt($ch, CURLOPT_USERPWD, $api_key . ':nopass');
            }
        }

        curl_setopt($ch, CURLOPT_USERAGENT, $call_options['userAgent']);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, CS_REST_SOCKET_TIMEOUT);
        curl_setopt($ch, CURLOPT_TIMEOUT, CS_REST_CALL_TIMEOUT);

        $inflate_response = false;
        if ($this->_curl_zlib) {
            $this->_log->log_message('curl+zlib support available. Requesting gzipped response.',
                get_class($this), LogInterface::LEVEL_VERBOSE);
            curl_setopt($ch, CURLOPT_ENCODING, 'gzip');
        } elseif (function_exists('gzinflate')) {
            $headers[] = 'Accept-Encoding: gzip';
            $inflate_response = true;
        }

        if ($call_options['protocol'] === 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);

            if (strlen(ini_get('curl.cainfo')) === 0) {
                curl_setopt($ch, CURLOPT_CAINFO, dirname(__FILE__) . '/cacert.pem');
            }
        }

        switch ($call_options['method']) {
            case TransportInterface::CS_REST_PUT:
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, TransportInterface::CS_REST_PUT);
                $headers[] = 'Content-Length: ' . strlen($call_options['data']);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $call_options['data']);
                break;
            case TransportInterface::CS_REST_POST:
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, isset($call_options['data']) ? $call_options['data'] : '');
                break;
            case TransportInterface::CS_REST_DELETE:
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, TransportInterface::CS_REST_DELETE);
                break;
        }

        if (count($headers) > 0) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }

        $response = curl_exec($ch);

        if (!$response && $response !== '') {
            $this->_log->log_message('Error making request with curl_error: ' . curl_errno($ch),
                get_class($this), LogInterface::LEVEL_ERROR);

            throw new CurlException(curl_error($ch), curl_errno($ch));
        }

        list(, $result) = $this->split_and_inflate($response, $inflate_response);

        $this->_log->log_message('API Call Info for ' . $call_options['method'] . ' ' .
            curl_getinfo($ch, CURLINFO_EFFECTIVE_URL) . ': ' . curl_getinfo($ch, CURLINFO_SIZE_UPLOAD) .
            ' bytes uploaded. ' . curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD) . ' bytes downloaded' .
            ' Total time (seconds): ' . curl_getinfo($ch, CURLINFO_TOTAL_TIME),
            get_class($this), LogInterface::LEVEL_VERBOSE);

        $result = array(
            'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
            'response' => $result
        );

        curl_close($ch);

        return $result;
    }
}
