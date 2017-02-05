<?php

namespace CreateSend\Socket;

interface WrapperInterface
{
    /**
     * @param string $domain
     * @param int $port
     * @return bool
     */
    public function open($domain, $port);

    /**
     * @param string $data
     */
    public function write($data);

    /**
     * @return string
     */
    public function read();

    public function close();
}