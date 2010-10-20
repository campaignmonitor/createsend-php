<?php
require_once 'csrest.php';

define('CS_REST_CUSTOM_FIELD_TYPE_TEXT', 'Text');
define('CS_REST_CUSTOM_FIELD_TYPE_NUMBER', 'Number');
define('CS_REST_CUSTOM_FIELD_TYPE_MULTI_SELECTONE', 'MultiSelectOne');
define('CS_REST_CUSTOM_FIELD_TYPE_MULTI_SELECTMANY', 'MultiSelectMany');
define('CS_REST_CUSTOM_FIELD_TYPE_DATE', 'Date');
define('CS_REST_CUSTOM_FIELD_TYPE_COUNTRY', 'Country');
define('CS_REST_CUSTOM_FIELD_TYPE_USSTATE', 'USState');

/**
 * Class to access a lists resources from the create send API. 
 * This class includes functions to create lists and custom fields, 
 * along with accessing the subscribers of a specific list
 * @author tobyb
 *
 */
class CS_REST_Lists extends CS_REST_Wrapper_Base {	
	
	/**
	 * The base route of the lists resource.
	 * @var string
	 * @access private
	 */
	var $_lists_base_route;
	
	/**
	 * Constructor. 
	 * @param $list_id string The list id to access (Ignored for create requests)
	 * @param $api_key string Your api key (Ignored for get_apikey requests)
	 * @param $protocol string The protocol to use for requests (http|https)
	 * @param $debug_level int The level of debugging required CS_REST_LOG_NONE | CS_REST_LOG_ERROR | CS_REST_LOG_WARNING | CS_REST_LOG_VERBOSE
	 * @param $host string The host to send API requests to. There is no need to change this
	 * @param $log CS_REST_Log The logger to use. Used for dependency injection
	 * @param $serialiser The serialiser to use. Used for dependency injection
	 * @param $transport The transport to use. Used for dependency injection
	 * @access public
	 */
	function CS_REST_Lists (
		$list_id,
		$api_key, 
		$protocol = 'https', 
		$debug_level = CS_REST_LOG_NONE,
		$host = 'api.createsend.com', 
		$log = NULL,
		$serialiser = NULL, 
		$transport = NULL) {
		$this->CS_REST_Wrapper_Base($api_key, $protocol, $debug_level, $host, $log, $serialiser, $transport);
		$this->_lists_base_route = $this->_base_route.'lists/'.$list_id.'/';		
	}
	
	function create($client_id, $list_details, $call_options = array()) {
		$list_details = $this->_serialiser->format_item('List', $list_details);
		
		$call_options['route'] = $this->_base_route.'lists/'.$client_id.'.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_POST;
		$call_options['data'] = $this->_serialiser->serialise($list_details);
		
		return $this->_call($call_options);		
	}
	
	function update($list_details, $call_options = array()) {
		$list_details = $this->_serialiser->format_item('List', $list_details);
		
		$call_options['route'] = trim($this->_lists_base_route, '/').'.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_PUT;
		$call_options['data'] = $this->_serialiser->serialise($list_details);
		
		return $this->_call($call_options);		
	}
	
	function create_custom_field($custom_field_details, $call_options = array()) {
		if(isset($custom_field_details['Options']) && is_array($custom_field_details['Options'])) {
			$custom_field_details['Options'] = 
			    $this->_serialiser->format_item('Option', $custom_field_details['Options']);
		}
		
		$custom_field_details = $this->_serialiser->format_item('CustomField', $custom_field_details);
		
		$call_options['route'] = $this->_lists_base_route.'customfields.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_POST;
		$call_options['data'] = $this->_serialiser->serialise($custom_field_details);
		
		return $this->_call($call_options);	
	}
	
	function delete($call_options = array()) {
		$call_options['route'] = trim($this->_lists_base_route, '/').'.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_DELETE;
		
		return $this->_call($call_options);
	}
	
	function delete_custom_field($key, $call_options = array()) {
		$call_options['route'] = $this->_lists_base_route.'customfields/'.$key.'.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_DELETE;
		
		return $this->_call($call_options);		
	}
	
	function get_custom_fields($call_options = array()) {
		$call_options['route'] = $this->_lists_base_route.'customfields.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);		
	}
	
	function get_active_subscribers($added_since, $call_options = array()) {
		$call_options['route'] = $this->_lists_base_route.'active.'.
		    $this->_serialiser->get_format().'?date='.$added_since;
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);		
	}
	
	function get_bounced_subscribers($bounced_since, $call_options = array()) {
		$call_options['route'] = $this->_lists_base_route.'bounced.'.
		    $this->_serialiser->get_format().'?date='.$bounced_since;
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);		
	}
	
	function get_unsubscribed_subscribers($unsubscribed_since, $call_options = array()) {
		$call_options['route'] = $this->_lists_base_route.'unsubscribed.'.
		    $this->_serialiser->get_format().'?date='.$unsubscribed_since;
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);		
	}
	
	function get($call_options = array()) {
		$call_options['route'] = trim($this->_lists_base_route, '/').'.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);		
	}
	
	function get_stats($call_options = array()) {
		$call_options['route'] = $this->_lists_base_route.'stats.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);		
	}
}