<?php

namespace CreateSend\Log;

interface LogInterface
{
    const LEVEL_VERBOSE = 1000;
    const LEVEL_WARNING = 500;
    const LEVEL_ERROR = 250;
    const LEVEL_NONE = 0;

    public function log_message($message, $module, $level);
}
