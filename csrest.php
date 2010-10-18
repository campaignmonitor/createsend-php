<?php

require_once 'class/serialisation.php';
require_once 'class/transport.php';
require_once 'class/log.php';

define('CS_REST_WRAPPER_VERSION', '1.0.0');
define('CS_REST_GET', 'GET');
define('CS_REST_POST', 'POST');
define('CS_REST_PUT', 'PUT');
define('CS_REST_DELETE', 'DELETE');

/**
 * Base class for the 
 * @author tobyb
 *
 */
class CS_REST_Wrapper_Base {
	var $_api_host;
	var $_protocol;
	var $_base_route;
	
	var $_serialiser;
	var $_transport;
	
	var $_log;
	
	var $_default_call_options;
	
	function CS_REST_Wrapper_Base(
		$api_key, 
		$protocol = 'https', 
		$debug_level = CS_REST_LOG_NONE,
		$host = 'api.createsend.com', 
		$log = NULL,
		$serialiser = NULL, 
		$transport = NULL) {
			
		$this->_log = is_null($log) ? new CS_REST_Log($debug_level) : $log;	
			
		$this->_api_host = $host;
		$this->_protocol = $protocol;
		$this->_base_route = $protocol.'://'.$host.'/api/v3/';
		
		$this->_log->log_message('Creating wrapper for '.$this->_base_route, get_class($this), CS_REST_LOG_VERBOSE);
				
		$this->_transport = is_null($transport) ? 
		    @CS_REST_TransportFactory::get_available_transport($this->is_secure(), $this->_log) :
		    $transport; 
		    
		$this->_log->log_message('Using '.$this->_transport->get_type().' for transport', get_class($this), CS_REST_LOG_WARNING);
		    
		$this->_serialiser = is_null($serialiser) ?
			@CS_REST_SerialiserFactory::get_available_serialiser($this->_log) :
			$serialiser;
			
		$this->_log->log_message('Using '.$this->_serialiser->get_format().' data format', get_class($this), CS_REST_LOG_WARNING);
		
		$this->_default_call_options = array (
		    'credentials' => $api_key.':nopass',
		    'userAgent' => 'CS_REST_Wrapper v'.CS_REST_WRAPPER_VERSION,
		    'contentType' => $this->_serialiser->get_content_type().'; charset=utf-8', 
			'deserialise' => true,
			'host' => $this->_api_host
		);		
	}
	
	/**
	 * @return boolean True if the wrapper is using SSL.
	 * @access public
	 */
	function is_secure() {
		return $this->_protocol === 'https';
	}
	
	/**
	 * Can be used to check if a call to the api resulted in a successful response.
	 * @param $result The result of any of the wrapper methods
	 * @return boolean False if the call failed. Check the response property for the failure reason.
	 */
	function was_successful($result) {
		return $result['code'] >= 200 && $result['code'] < 300;
	}
	
	function _call($call_options) {
		$call_options = array_merge($this->_default_call_options, $call_options);	
		$this->_log->log_message('Making '.$call_options['method'].' call to: '.$call_options['route'], get_class($this), CS_REST_LOG_WARNING);	
			
	    $call_result = $this->_transport->make_call($call_options);
	    	    
	    $this->_log->log_message('Call result: <pre>'.$call_result.'</pre>', get_class($this), CS_REST_LOG_VERBOSE);
	    
	    if($call_options['deserialise']) {
	    	$call_result['response'] = $this->_serialiser->deserialise($call_result['response']);
	    }
	    
	    return $call_result;
	}
	
	function get_timezones($call_options = array()) {
		$call_options['route'] = $this->_base_route.'timezones.'.$this->_serialiser->get_format();	
		$call_options['method'] = CS_REST_GET;
			
		return $this->_call($call_options);		
	}
	
	function get_systemdate($call_options = array()) {
		$call_options['route'] = $this->_base_route.'systemdate.'.$this->_serialiser->get_format();	
		$call_options['method'] = CS_REST_GET;
			
		return $this->_call($call_options);		
	}
	
	function get_countries($call_options = array()) {
		$call_options['route'] = $this->_base_route.'countries.'.$this->_serialiser->get_format();	
		$call_options['method'] = CS_REST_GET;
			
		return $this->_call($call_options);		
	}
	
	function get_apikey($username, $password, $site_url, $call_options = array()) {
		$call_options['route'] = $this->_base_route.'apikey.'.$this->_serialiser->get_format().
		    '?siteurl='.$site_url;
		$call_options['method'] = CS_REST_GET;
		
		$call_options['credentials'] = $username.':'.$password;
		
		return $this->_call($call_options);
	}
	
	function get_clients($call_options = array()) {
		$call_options['route'] = $this->_base_route.'clients.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_GET;
			
		return $this->_call($call_options);
	}
}