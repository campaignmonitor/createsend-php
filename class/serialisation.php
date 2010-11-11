<?php

require_once 'services_json.php';

class CS_REST_SerialiserFactory {
    function get_available_serialiser($log) {
        $log->log_message('Getting serialiser', get_class($this), CS_REST_LOG_VERBOSE);
        if(@CS_REST_NativeJsonSerialiser::is_available()) {
            return new CS_REST_NativeJsonSerialiser($log);
        } else {
            return new CS_REST_ServicesJsonSerialiser($log);
        }
    }
}

class CS_REST_NativeJsonSerialiser {

    var $_log;

    function CS_REST_NativeJsonSerialiser($log) {
        $this->_log = $log;
    }

    function get_format() {
        return 'json';
    }
    
    function get_type() {
        return 'native';
    }

    /**
     * Tests if this serialisation scheme is available on the current server
     * @return boolean False if the server doesn't support the serialisation scheme
     */
    function is_available() {
        return function_exists('json_decode') && function_exists('json_encode');
    }

    function serialise($data) {
        return json_encode($data);
    }

    function deserialise($text) {
        $data = json_decode($text);

        return is_null($data) ? $text : $data;
    }
}

class CS_REST_ServicesJsonSerialiser {
    
    var $_log;
    var $_serialiser;
    
    function CS_REST_ServicesJsonSerialiser($log) {
        $this->_log = $log;
        $this->_serialiser = new Services_JSON();
    }

    function get_content_type() {
        return 'application/json';
    }

    function get_format() {
        return 'json';
    }
    
    function get_type() {
        return 'services_json';
    }
    
    function serialise($data) {
        return $this->_serialiser->encode($data);
    }
    
    function deserialise($text) {
        $data = $this->_serialiser->decode($text);
        
        return is_null($data) ? $text : $data;
    }
}
