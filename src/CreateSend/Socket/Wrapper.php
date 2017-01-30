<?php

namespace CreateSend\Socket;

class Wrapper implements WrapperInterface
{
    public $socket;

    /**
     * {@inheritdoc}
     */
    public function open($domain, $port)
    {
        $this->socket = fsockopen($domain, $port, $errno, $errstr, CS_REST_SOCKET_TIMEOUT);

        if (!$this->socket) {
            die('Error making request with ' . $errno . ': ' . $errstr);

        }

        if (function_exists('stream_set_timeout')) {
            stream_set_timeout($this->socket, CS_REST_SOCKET_TIMEOUT);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function write($data)
    {
        fwrite($this->socket, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        ob_start();
        fpassthru($this->socket);

        return ob_get_clean();
    }

    /**
     * {@inheritdoc}
     */
    public function close()
    {
        fclose($this->socket);
    }
}
