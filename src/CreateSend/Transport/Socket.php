<?php

namespace CreateSend\Transport;

use CreateSend\Log\LogInterface;
use CreateSend\Socket\Wrapper;
use CreateSend\Socket\WrapperInterface;

class Socket extends Base implements TransportInterface
{
    /**
     * @var WrapperInterface
     */
    private $_socket_wrapper;

    /**
     * @param LogInterface $log
     * @param WrapperInterface $socket_wrapper
     */
    public function __construct(LogInterface $log, WrapperInterface $socket_wrapper = null)
    {
        parent::__construct($log);

        if (is_null($socket_wrapper)) {
            $socket_wrapper = new Wrapper();
        }

        $this->_socket_wrapper = $socket_wrapper;
    }

    /**
     * {@inheritdoc}
     */
    public function get_type()
    {
        return 'Socket';
    }

    /**
     * {@inheritdoc}
     */
    public function make_call($call_options)
    {
        $start_host = strpos($call_options['route'], $call_options['host']);
        $host_len = strlen($call_options['host']);

        $domain = substr($call_options['route'], $start_host, $host_len);
        $host = $domain;
        $path = substr($call_options['route'], $start_host + $host_len);
        $protocol = substr($call_options['route'], 0, $start_host);
        $port = 80;

        $this->_log->log_message(
            'Creating socket to ' . $domain . ' over ' . $protocol . ' for request to ' . $path,
            get_class($this),
            LogInterface::LEVEL_VERBOSE
        );

        if ($protocol === 'https://') {
            $domain = 'ssl://' . $domain;
            $port = 443;
        }

        if ($this->_socket_wrapper->open($domain, $port)) {
            $inflate_response = function_exists('gzinflate');

            $request = $this->_build_request($call_options, $host, $path, $inflate_response);
            $this->_log->log_message(
                'Sending <pre>' . $request . '</pre> down the socket',
                get_class($this),
                LogInterface::LEVEL_VERBOSE
            );

            $this->_socket_wrapper->write($request);
            $response = $this->_socket_wrapper->read();
            $this->_socket_wrapper->close();

            $this->_log->log_message(
                'API Call Info for ' . $call_options['method'] . ' ' . $call_options['route'] . ': ' . strlen($request) . ' bytes uploaded. ' . strlen($response) . ' bytes downloaded',
                get_class($this),
                LogInterface::LEVEL_VERBOSE
            );

            list($headers, $result) = $this->split_and_inflate($response, $inflate_response);

            $this->_log->log_message(
                'Received headers <pre>' . $headers . '</pre>',
                get_class($this),
                LogInterface::LEVEL_VERBOSE
            );

            return array(
                'code' => $this->_get_status_code($headers),
                'response' => trim($result)
            );
        }
    }

    /**
     * @param string $headers
     * @return string
     */
    public function _get_status_code($headers)
    {
        if (preg_match('%^\s*HTTP/1\.1 (?P<code>\d{3})%', $headers, $regs)) {
            $this->_log->log_message(
                'Got HTTP Status Code: ' . $regs['code'],
                get_class($this),
                LogInterface::LEVEL_VERBOSE
            );

            return $regs['code'];
        }

        $this->_log->log_message(
            'Failed to get HTTP status code from request headers <pre>' . $headers . '</pre>',
            get_class($this),
            LogInterface::LEVEL_ERROR
        );

        trigger_error('Failed to get HTTP status code from request', E_USER_ERROR);
    }

    public function _build_request($call_options, $host, $path, $accept_gzip)
    {
        $request_auth_details = '';

        if (array_key_exists('authdetails', $call_options)) {
            if (array_key_exists('username', $call_options['authdetails']) &&
                array_key_exists('password', $call_options['authdetails'])
            ) {
                # Authenticating using basic auth for retrieving user's API key.
                $request_auth_details .= 'Authorization: Basic ' . base64_encode($call_options['authdetails']['username'] . ':' . $call_options['authdetails']['password']) . "\n";
            } elseif (array_key_exists('access_token', $call_options['authdetails'])) {
                # Authenticating using OAuth.
                $access_token = $call_options['authdetails']['access_token'];
                $request_auth_details .= 'Authorization: Bearer ' . $access_token . "\n";
            } elseif (array_key_exists('api_key', $call_options['authdetails'])) {
                # Authenticating using an API key.
                $api_key = $call_options['authdetails']['api_key'];
                $request_auth_details .= 'Authorization: Basic ' . base64_encode($api_key . ':nopass') . "\n";
            }
        }

        $request =
            $call_options['method'] . ' ' . $path . " HTTP/1.1\n" .
            'Host: ' . $host . "\n" .
            $request_auth_details .
            'User-Agent: ' . $call_options['userAgent'] . "\n" .
            "Connection: Close\n" .
            'Content-Type: ' . $call_options['contentType'] . "\n";

        if ($accept_gzip) {
            $request .=
                "Accept-Encoding: gzip\n";
        }

        if (isset($call_options['data'])) {
            $request .=
                'Content-Length: ' . strlen($call_options['data']) . "\n\n" .
                $call_options['data'];
        }

        return $request . "\n\n";
    }
}
