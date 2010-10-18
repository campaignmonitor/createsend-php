<?php
require_once 'csrest.php';

class CS_REST_Clients extends CS_REST_Wrapper_Base {
	
	var $_clients_base_route;
	
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
}