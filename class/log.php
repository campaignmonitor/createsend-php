<?php
defined('CS_REST_LOG_VERBOSE') or define('CS_REST_LOG_VERBOSE', 1000);
defined('CS_REST_LOG_WARNING') or define('CS_REST_LOG_WARNING', 500);
defined('CS_REST_LOG_ERROR') or define('CS_REST_LOG_ERROR', 250);
defined('CS_REST_LOG_NONE') or define('CS_REST_LOG_NONE', 0);

class CS_REST_Log {
    public $_level;

    public function __construct($level) {
        $this->_level = $level;
    }

    public function log_message($message, $module, $level) {
        if($this->_level >= $level) {
            echo date('G:i:s').' - '.$module.': '.$message."<br />\n";
        }
    }
}
