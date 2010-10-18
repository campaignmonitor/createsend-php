<?php

class CS_REST_TransportFactory {
	function get_available_transport($requires_ssl, $log) {		
		if(@CS_REST_CurlTransport::is_available($requires_ssl)) {
			return new CS_REST_CurlTransport($log);
		} else if(@CS_REST_SocketTransport::is_available($requires_ssl)) {
			return new CS_REST_SocketTransport($log);
		} else {
			trigger_error('No transport is available.'.
			    ($requires_ssl ? ' Try using non-secure (http) mode or ' : ' Please ').
			    'ensure the cURL extension is loaded',
				E_ERROR);
		}
	}
}

/**
 * Provide HTTP request functionality via cURL extensions
 * 
 * @author tobyb
 * @since 1.0
 */
class CS_REST_CurlTransport {
	
	var $_log;
	
	function CS_REST_CurlTransport($log) {
		$this->_log = $log;
	}
	
	/**
	 * @return string The type of transport used
	 */
	function get_type() {
		return 'cURL';
	}
	
	/**
	 * Check's if this transport schema may be used on the current server
	 * 
	 * @static
	 * @param $requires_ssl 
	 * 
	 * @return boolean False if this schema is unavailable on the server.
	 */
	function is_available($requires_ssl = false) {
		return function_exists('curl_init') &&
		       function_exists('curl_exec');
	}
	
	function make_call($call_options) {
        $ch = curl_init();
        
        curl_setopt($ch, CURLOPT_URL, $call_options['route']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, $call_options['credentials']);
        curl_setopt($ch, CURLOPT_USERAGENT, $call_options['userAgent']);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: '.$call_options['contentType']));
        
        switch($call_options['method']) {
        	case 'PUT':		
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Length: '.strlen($call_options['data'])));
				curl_setopt($ch, CURLOPT_POSTFIELDS, $call_options['data']);
				break;
        	case 'POST':		
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $call_options['data']);
				break;
        	case 'DELETE':
				curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
				break;
        }
		
		$response = curl_exec($ch);
		if(!$response) {
			trigger_error(curl_error($ch));
		}
		
		$this->_log->log_message('API Call Info for '.$call_options['method'].' '.
		    curl_getinfo($ch, CURLINFO_EFFECTIVE_URL).': '.curl_getinfo($ch, CURLINFO_SIZE_UPLOAD).
		    ' bytes uploaded. '.curl_getinfo($ch, CURLINFO_SIZE_DOWNLOAD).' bytes downloaded'.
		    ' Total time (seconds): '.curl_getinfo($ch, CURLINFO_TOTAL_TIME), 
		    get_class($this), CS_REST_LOG_VERBOSE);
		
		$result = array(
			'code' => curl_getinfo($ch, CURLINFO_HTTP_CODE),
		    'response' => $response
		);
		
		curl_close($ch);
		
		return $result;
	}
}

class CS_REST_SocketTransport {	
	
	var $_log;
	
	function CS_REST_SocketTransport($log) {
		$this->_log = $log;
	}
	
	/**
	 * @return string The type of transport used
	 */
	function get_type() {
		return 'Socket';
	}
	
    /**
     * Check's if this transport schema may be used on the current server
     * 
     * @static
     * @param $requires_ssl
     * 
     * @return boolean False if this schema is unavailable on the server.
     */
	function is_available($requires_ssl = false) {
		if(function_exists('fsockopen')) {
		    if($requires_ssl) {
		    	return extension_loaded('openssl');
		    }
		    
		    return true;
		}
		
		return false;
	}		
}