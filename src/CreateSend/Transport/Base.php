<?php

namespace CreateSend\Transport;

use CreateSend\Log\LogInterface;

abstract class Base
{
    /**
     * @var LogInterface
     */
    protected $_log;

    /**
     * @param LogInterface $log
     */
    public function __construct(LogInterface $log)
    {
        $this->_log = $log;
    }

    /**
     * @param string $response
     * @param bool $may_be_compressed
     * @return array
     */
    public function split_and_inflate($response, $may_be_compressed)
    {
        $ra = explode("\r\n\r\n", $response);

        $result = array_pop($ra);
        $headers = array_pop($ra);

        if ($may_be_compressed && preg_match('/^Content-Encoding:\s+gzip\s+$/im', $headers)) {
            $original_length = strlen($response);
            $result = gzinflate(substr($result, 10, -8));

            $this->_log->log_message('Inflated gzipped response: ' . $original_length . ' bytes ->' .
                strlen($result) . ' bytes', get_class(), LogInterface::LEVEL_VERBOSE);
        }

        return array($headers, $result);
    }
}
