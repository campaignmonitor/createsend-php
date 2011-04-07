<?php

require_once dirname(__FILE__).'/services_json.php';

class CS_REST_SerialiserFactory {
    function get_available_serialiser($log) {
        $log->log_message('Getting serialiser', get_class($this), CS_REST_LOG_VERBOSE);
        if(@CS_REST_NativeJsonSerialiser::is_available()) {
            return new CS_REST_NativeJsonSerialiser($log);
        } else {
            return new CS_REST_ServicesJsonSerialiser($log);
        }
    }
    
    /**
     * Recursively ensures that all data values are utf-8 encoded. 
     * @param array $data All values of this array are checked for utf-8 encoding. 
     */
    function check_encoding($data) {
        foreach($data as $k => $v) {
            // If the element is a sub-array then recusively encode the array
            if(is_array($v)) {
                $data[$k] = @CS_REST_SerialiserFactory::check_encoding($v);
            // Otherwise if the element is a string then we need to check the encoding
            } else if(is_string($v)) {
                if((function_exists('mb_detect_encoding') && mb_detect_encoding($v) !== 'UTF-8') || 
                   (function_exists('mb_check_encoding') && !mb_check_encoding($v, 'UTF-8'))) {
                    // The string is using some other encoding, make sure we utf-8 encode
                    $v = utf8_encode($v);       
                }
                
                $data[$k] = $v;
            }
        }
              
        return $data;
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
        return json_encode(@CS_REST_SerialiserFactory::check_encoding($data));
    }

    function deserialise($text) {
        $data = json_decode($text);

        return $this->strip_surrounding_quotes(is_null($data) ? $text : $data);
    }
    
    /** 
     * We've had sporadic reports of people getting ID's from create routes with the surrounding quotes present. 
     * There is no case where these should be present. Just get rid of it. 
     */
    function strip_surrounding_quotes($data) {
        if(is_string($data)) {
            return trim($data, '"');
        }
        
        return $data;
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
        return $this->_serialiser->encode(@CS_REST_SerialiserFactory::check_encoding($data));
    }
    
    function deserialise($text) {
        $data = $this->_serialiser->decode($text);
        
        return is_null($data) ? $text : $data;
    }
}
