<?php
require_once 'csrest.php';

/**
 * Class to access a campaigns resources from the create send API. 
 * This class includes functions to create and send campaigns, 
 * along with accessing lists of campaign specific resources i.e reporting statistics
 * @author tobyb
 *
 */
class CS_REST_Campaigns extends CS_REST_Wrapper_Base {	
	
	/**
	 * The base route of the campaigns resource.
	 * @var string
	 * @access private
	 */
	var $_campaigns_base_route;
	
	/**
	 * Constructor. 
	 * @param $campaign_id string The campaign id to access (Ignored for create requests)
	 * @param $api_key string Your api key (Ignored for get_apikey requests)
	 * @param $protocol string The protocol to use for requests (http|https)
	 * @param $debug_level int The level of debugging required CS_REST_LOG_NONE | CS_REST_LOG_ERROR | CS_REST_LOG_WARNING | CS_REST_LOG_VERBOSE
	 * @param $host string The host to send API requests to. There is no need to change this
	 * @param $log CS_REST_Log The logger to use. Used for dependency injection
	 * @param $serialiser The serialiser to use. Used for dependency injection
	 * @param $transport The transport to use. Used for dependency injection
	 * @access public
	 */
	function CS_REST_Campaigns (
		$campaign_id,
		$api_key, 
		$protocol = 'https', 
		$debug_level = CS_REST_LOG_NONE,
		$host = 'api.createsend.com', 
		$log = NULL,
		$serialiser = NULL, 
		$transport = NULL) {
		$this->CS_REST_Wrapper_Base($api_key, $protocol, $debug_level, $host, $log, $serialiser, $transport);
		$this->_campaigns_base_route = $this->_base_route.'campaigns/'.$campaign_id.'/';		
	}
	
	function create($client_id, $campaign_info, $call_options = array()) {
		if(isset($campaign_info['ListIDs']) && is_array($campaign_info['ListIDs'])) {			
			$campaign_info['ListIDs'] = $this->_serialiser->format_list('ListID', $campaign_info['ListIDs']);
		}
		
		if(isset($campaign_info['Segments']) && is_array($campaign_info['Segments'])) {
			$campaign_info['Segments'] = $this->_serialiser->format_list('Segment', $campaign_info['Segments']);
		}
		
		$call_options['route'] = $this->_base_route.'campaigns/'.$client_id.'.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_POST;
		$call_options['data'] = $this->_serialiser->serialise($campaign_info);
		
		return $this->_call($call_options);		
	}
	
	function send($schedule, $call_options = array()) {
		$call_options['route'] = $this->_campaigns_base_route.'send.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_POST;
		$call_options['data'] = $this->_serialiser->serialise($schedule);
		
		return $this->_call($call_options);		
	}
	
	function delete($call_options = array()) {
		$call_options['route'] = trim($this->_campaigns_base_route, '/').'.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_DELETE;
		
		return $this->_call($call_options);
	}
	
	function get_bounces($call_options = array()) {
		$call_options['route'] = $this->_campaigns_base_route.'bounces.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);
	}
	
	function get_lists($call_options = array()) {
		$call_options['route'] = $this->_campaigns_base_route.'lists.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);
	}
	
	function get_summary($call_options = array()) {
		$call_options['route'] = $this->_campaigns_base_route.'summary.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);
	}
	
	function get_opens($since, $call_options = array()) {		
		$call_options['route'] = $this->_campaigns_base_route.'opens.'.
		    $this->_serialiser->get_format().'?date='.$since;
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);
	}
	
	function get_clicks($since, $call_options = array()) {		
		$call_options['route'] = $this->_campaigns_base_route.'clicks.'.
		    $this->_serialiser->get_format().'?date='.$since;
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);
	}
	
	function get_unsubscribes($since, $call_options = array()) {		
		$call_options['route'] = $this->_campaigns_base_route.'unsubscribes.'.
		    $this->_serialiser->get_format().'?date='.$since;
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);
	}
}