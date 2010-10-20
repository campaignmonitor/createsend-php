<?php
require_once 'csrest.php';

define('CS_REST_CLIENT_ACCESS_NONE', 0x0);
define('CS_REST_CLIENT_ACCESS_REPORTS', 0x1);
define('CS_REST_CLIENT_ACCESS_SUBSCRIBERS', 0x2);
define('CS_REST_CLIENT_ACCESS_CREATESEND', 0x4);
define('CS_REST_CLIENT_ACCESS_DESIGNSPAMTEST', 0x8);
define('CS_REST_CLIENT_ACCESS_IMPORTSUBSCRIBERS', 0x10);
define('CS_REST_CLIENT_ACCESS_IMPORTURL', 0x20);

/**
 * Class to access a clients resources from the create send API. 
 * This class includes functions to create and edit clients, 
 * along with accessing lists of client specific resources e.g campaigns
 * @author tobyb
 *
 */
class CS_REST_Clients extends CS_REST_Wrapper_Base {	
	
	/**
	 * The base route of the clients resource.
	 * @var string
	 * @access private
	 */
	var $_clients_base_route;
	
	/**
	 * Constructor. 
	 * @param $client_id string The client id to access (Ignored for create requests)
	 * @param $api_key string Your api key (Ignored for get_apikey requests)
	 * @param $protocol string The protocol to use for requests (http|https)
	 * @param $debug_level int The level of debugging required CS_REST_LOG_NONE | CS_REST_LOG_ERROR | CS_REST_LOG_WARNING | CS_REST_LOG_VERBOSE
	 * @param $host string The host to send API requests to. There is no need to change this
	 * @param $log CS_REST_Log The logger to use. Used for dependency injection
	 * @param $serialiser The serialiser to use. Used for dependency injection
	 * @param $transport The transport to use. Used for dependency injection
	 * @access public
	 */
	function CS_REST_Clients(
		$client_id,
		$api_key, 
		$protocol = 'https', 
		$debug_level = CS_REST_LOG_NONE,
		$host = 'api.createsend.com', 
		$log = NULL,
		$serialiser = NULL, 
		$transport = NULL) {
		$this->CS_REST_Wrapper_Base($api_key, $protocol, $debug_level, $host, $log, $serialiser, $transport);
		$this->_clients_base_route = $this->_base_route.'clients/'.$client_id.'/';		
	}
	
	function get_campaigns($call_options = array()) {
		$call_options['route'] = $this->_clients_base_route.'campaigns.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_GET;
	
		return $this->_call($call_options);
	}
	
	function get_drafts($call_options = array()) {
		$call_options['route'] = $this->_clients_base_route.'drafts.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);
	}
	
	function get_lists($call_options = array()) {
		$call_options['route'] = $this->_clients_base_route.'lists.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);
	}
	
	function get_segments($call_options = array()) {
		$call_options['route'] = $this->_clients_base_route.'segments.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);
	}
	
	function get_suppressionlist($call_options = array()) {
		$call_options['route'] = $this->_clients_base_route.'suppressionlist.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);
	}
	
	function get_templates($call_options = array()) {
		$call_options['route'] = $this->_clients_base_route.'templates.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);
	}
	
	function get($call_options = array()) {
		$call_options['route'] = trim($this->_clients_base_route, '/').'.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);
	}	
	
	function delete($call_options = array()) {
		$call_options['route'] = $this->_base_route.'clients.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_DELETE;
		
		return $this->_call($call_options);
	}
	
	function create($client, $call_options = array()) {
		$call_options['route'] = $this->_base_route.'clients.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_POST;
		$call_options['data'] = $this->_serialiser->serialise($client);
		
		return $this->_call($call_options);
	}
	
	function set_basics($client_basics, $call_options = array()) {
		$call_options['route'] = $this->_clients_base_route.'setbasics.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_PUT;
		$call_options['data'] = $this->_serialiser->serialise($client_basics);
		
		return $this->_call($call_options);
	}
	
	function set_access($client_access, $call_options = array()) {
		$call_options['route'] = $this->_clients_base_route.'setaccess.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_PUT;
		$call_options['data'] = $this->_serialiser->serialise($client_access);
		
		return $this->_call($call_options);
	}
	
	function set_payg_billing($client_billing, $call_options = array()) {
		$call_options['route'] = $this->_clients_base_route.'setpaygbilling.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_PUT;
		$call_options['data'] = $this->_serialiser->serialise($client_billing);
		
		return $this->_call($call_options);
	}
	
	function set_monthly_billing($client_billing, $call_options = array()) {
		$call_options['route'] = $this->_clients_base_route.'setmonthlybilling.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_PUT;
		$call_options['data'] = $this->_serialiser->serialise($client_billing);
		
		return $this->_call($call_options);
	}
}