<?php

if (!class_exists('Services_JSON')) {
    require_once dirname(__FILE__).'/services_json.php';
}

function CS_REST_SERIALISATION_get_available($log) {
    $log->log_message('Getting serialiser', __FUNCTION__, CS_REST_LOG_VERBOSE);
    if(function_exists('json_decode') && function_exists('json_encode')) {
        return new CS_REST_NativeJsonSerialiser($log);
    } else {
        return new CS_REST_ServicesJsonSerialiser($log);
    }
}
class CS_REST_BaseSerialiser {

    var $_log;

    function CS_REST_BaseSerialiser($log) {
        $this->_log = $log;
    }

    /**
     * Recursively ensures that all data values are utf-8 encoded.
     * @param array $data All values of this array are checked for utf-8 encoding.
     */
    function check_encoding($data) {
        foreach($data as $k => $v) {
            // If the element is a sub-array then recusively encode the array
            if(is_array($v)) {
                $data[$k] = $this->check_encoding($v);
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

class CS_REST_NativeJsonSerialiser extends CS_REST_BaseSerialiser {

    function CS_REST_NativeJsonSerialiser($log) {
        $this->CS_REST_BaseSerialiser($log);
    }

    function get_format() {
        return 'json';
    }

    function get_type() {
        return 'native';
    }

    function serialise($data) {
    	if(is_null($data) || $data == '') return '';
        return json_encode($this->check_encoding($data));
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

class CS_REST_ServicesJsonSerialiser extends CS_REST_BaseSerialiser {

    var $_serialiser;

    function CS_REST_ServicesJsonSerialiser($log) {
        $this->CS_REST_BaseSerialiser($log);
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
    	if(is_null($data) || $data == '') return '';
        return $this->_serialiser->encode($this->check_encoding($data));
    }

    function deserialise($text) {
        $data = $this->_serialiser->decode($text);

        return is_null($data) ? $text : $data;
    }
}
