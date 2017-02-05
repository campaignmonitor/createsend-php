<?php

namespace CreateSend\Log;

class Log implements LogInterface
{
    private $_level;

    /**
     * @param int $level
     */
    public function __construct($level = LogInterface::LEVEL_NONE)
    {
        $this->_level = $level;
    }

    /**
     * {@inheritdoc}
     */
    public function log_message($message, $module, $level)
    {
        if ($this->_level >= $level) {
            echo date('G:i:s') . ' - ' . $module . ': ' . $message . "<br />\n";
        }
    }
}
