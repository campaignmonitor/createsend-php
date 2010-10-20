<?php
require_once 'csrest.php';

/**
 * Class to access a subscribers resources from the create send API. 
 * This class includes functions to add and remove subscribers , 
 * along with accessing statistics for a single subscriber
 * @author tobyb
 *
 */
class CS_REST_Subscribers extends CS_REST_Wrapper_Base {	
	
	/**
	 * The base route of the subscriber resource.
	 * @var string
	 * @access private
	 */
	var $_subscribers_base_route;
	
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
	function CS_REST_Subscribers (
		$list_id,
		$api_key, 
		$protocol = 'https', 
		$debug_level = CS_REST_LOG_NONE,
		$host = 'api.createsend.com', 
		$log = NULL,
		$serialiser = NULL, 
		$transport = NULL) {
		$this->CS_REST_Wrapper_Base($api_key, $protocol, $debug_level, $host, $log, $serialiser, $transport);
		$this->_subscribers_base_route = $this->_base_route.'subscribers/'.$list_id;		
	}
	
	function add($subscriber, $call_options = array()) {
		if(isset($subscriber['CustomFields']) && is_array($subscriber['CustomFields'])) {
			$subscriber['CustomFields'] = $this->_serialiser->format_item('CustomField', $subscriber['CustomFields']);
		}
		
		$subscriber = $this->_serialiser->format_item('Subscriber', $subscriber);
		
		$call_options['route'] = $this->_subscribers_base_route.'.'.
		    $this->_serialiser->get_format();
		$call_options['method'] = CS_REST_POST;
		$call_options['data'] = $this->_serialiser->serialise($subscriber);
		
		return $this->_call($call_options);
	}
	
	function import($subscribers, $resubscribe, $call_options = array()) {
		for ($i = 0; $i < count($subscribers); $i++) {
			if(isset($subscribers[$i]['CustomFields']) && is_array($subscribers[$i]['CustomFields'])) {
				$subscribers[$i]['CustomFields'] = 
				    $this->_serialiser->format_item('CustomField', $subscribers[$i]['CustomFields']);
			}
		}
		
		$call_options['route'] = $this->_subscribers_base_route.'/import.'.
		    $this->_serialiser->get_format();
		$call_options['method'] = CS_REST_POST;
		$call_options['data'] = $this->_serialiser->serialise(array(
		    'Resubscribe' => $resubscribe,
		    'Subscribers' => $subscribers
		));
		
		return $this->_call($call_options);
	}
	
	function get($email, $call_options = array()) {
		$call_options['route'] = $this->_subscribers_base_route.'.'.
		    $this->_serialiser->get_format().'?email='.$email;
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);	
	}
	
	function get_history($email, $call_options = array()) {
		$call_options['route'] = $this->_subscribers_base_route.'/history.'.
		    $this->_serialiser->get_format().'?email='.$email;
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);	
	}
	
	function unsubscribe($email, $call_options = array()) {
		$call_options['route'] = $this->_subscribers_base_route.'/unsubscribe.'.
		    $this->_serialiser->get_format().'?email='.$email;
		$call_options['method'] = CS_REST_POST;
		$call_options['data'] = 'email='.$email;
		
		return $this->_call($call_options);	
	}
	
	
}