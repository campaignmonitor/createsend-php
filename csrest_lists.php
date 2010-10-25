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
		$this->set_list_id($list_id);	
	}
    
    /**
     * Change the list id used for calls after construction
     * @param $list_id
     * @access public
     */
    function set_list_id($list_id) {
        $this->_lists_base_route = $this->_base_route.'lists/'.$list_id.'/'; 
    }
    
    /**
     * Creates a new list based on the provided details.
     * Both the UnsubscribePage and the ConfirmationSuccessPage parameters are optional
     * @param string $client_id The client to create the campaign for
     * @param array $list_details The list details to use during creation. 
     *     This array should be of the form 
     *     array(
     *         'Title' => string The list title
     *         'UnsubscribePage' => string The page to redirect subscribers to when they unsubscribe
     *         'ConfirmedOptIn' => boolean Whether this list requires confirmation of subscription
     *         'ConfirmationSuccessPage' => string The page to redirect subscribers to when 
     *             they confirm their subscription
     *     )
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (201)
     *     'response' => string The ID of the newly created list
     * )
     */
	function create($client_id, $list_details, $call_options = array()) {
		$list_details = $this->_serialiser->format_item('List', $list_details);
		
		$call_options['route'] = $this->_base_route.'lists/'.$client_id.'.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_POST;
		$call_options['data'] = $this->_serialiser->serialise($list_details);
		
		return $this->_call($call_options);		
	}
    
    /**
     * Updates the details of an existing list
     * Both the UnsubscribePage and the ConfirmationSuccessPage parameters are optional
     * @param string $client_id The client to create the campaign for
     * @param array $list_details The list details to use during creation. 
     *     This array should be of the form 
     *     array(
     *         'Title' => string The list title
     *         'UnsubscribePage' => string The page to redirect subscribers to when they unsubscribe
     *         'ConfirmedOptIn' => boolean Whether this list requires confirmation of subscription
     *         'ConfirmationSuccessPage' => string The page to redirect subscribers to when 
     *             they confirm their subscription
     *     )
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => string The HTTP response (It will be empty)
     * )
     */
	function update($list_details, $call_options = array()) {
		$list_details = $this->_serialiser->format_item('List', $list_details);
		
		$call_options['route'] = trim($this->_lists_base_route, '/').'.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_PUT;
		$call_options['data'] = $this->_serialiser->serialise($list_details);
		
		return $this->_call($call_options);		
	}
    
    /**
     * Creates a new custom field for the current list
     * @param array $custom_field_details The details of the new custom field. 
     *     This array should be of the form 
     *     array(
     *         'FieldName' => string The name of the new custom field
     *         'DataType' => string The data type of the new custom field 
     *             This should be one of 
     *             CS_REST_CUSTOM_FIELD_TYPE_TEXT
     *             CS_REST_CUSTOM_FIELD_TYPE_NUMBER
     *             CS_REST_CUSTOM_FIELD_TYPE_MULTI_SELECTONE
     *             CS_REST_CUSTOM_FIELD_TYPE_MULTI_SELECTMANY
     *             CS_REST_CUSTOM_FIELD_TYPE_DATE
     *             CS_REST_CUSTOM_FIELD_TYPE_COUNTRY
     *             CS_REST_CUSTOM_FIELD_TYPE_USSTATE
     *         'Options' => array<string> Valid options for either Multi-Optioned field data type
     *     )
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (201)
     *     'response' => string The Personalisation tag of the newly created custom field
     * )
     */
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
    
    /**
     * Deletes an existing list from the system
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => string The HTTP Response (It will be empty)
     * )
     */
	function delete($call_options = array()) {
		$call_options['route'] = trim($this->_lists_base_route, '/').'.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_DELETE;
		
		return $this->_call($call_options);
	}
    
    /**
     * Deletes an existing custom field from the system
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => string The HTTP Response (It will be empty)
     * )
     */
	function delete_custom_field($key, $call_options = array()) {
		$call_options['route'] = $this->_lists_base_route.'customfields/'.urlencode($key).'.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_DELETE;
		
		return $this->_call($call_options);		
	}
    
    /**
     * Gets a list of all custom fields defined for the current list
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => array(
     *         array(
     *             'FieldName' => The name of the custom field
     *             'Key' => The personalisation tag of the custom field
     *             'DataType' => The data type of the custom field
     *             'FieldOptions' => Valid options for a multi-optioned custom field
     *         )
     *     )
     * )
     */
	function get_custom_fields($call_options = array()) {
		$call_options['route'] = $this->_lists_base_route.'customfields.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);		
	}
	
	/**
	 * Gets all active subscribers added since the given date
	 * @param string $added_since The date to start getting subscribers from
	 * @param $call_options
	 * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => array(
     *         array(
     *             'EmailAddress' => The email address of the subscriber
     *             'Name' => The name of the subscriber
     *             'Date' => The date that the subscriber was added to the list
     *             'State' => The current state of the subscriber, will be 'Active'
     *             'CustomFields' => array (
     *                 array(
     *                     'Key' => The personalisation tag of the custom field
     *                     'Value' => The value of the custom field for this subscriber
     *                 )
     *             )
     *         )
     *     )
     * )
	 */
	function get_active_subscribers($added_since, $call_options = array()) {
		$call_options['route'] = $this->_lists_base_route.'active.'.
		    $this->_serialiser->get_format().'?date='.urlencode($added_since);
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);		
	}
    
    /**
     * Gets all bounced subscribers who have bounced out since the given date
     * @param string $added_since The date to start getting subscribers from
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => array(
     *         array(
     *             'EmailAddress' => The email address of the subscriber
     *             'Name' => The name of the subscriber
     *             'Date' => The date that the subscriber bounced out of the list
     *             'State' => The current state of the subscriber, will be 'Bounced'
     *             'CustomFields' => array (
     *                 array(
     *                     'Key' => The personalisation tag of the custom field
     *                     'Value' => The value of the custom field for this subscriber
     *                 )
     *             )
     *         )
     *     )
     * )
     */
	function get_bounced_subscribers($bounced_since, $call_options = array()) {
		$call_options['route'] = $this->_lists_base_route.'bounced.'.
		    $this->_serialiser->get_format().'?date='.urlencode($bounced_since);
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);		
	}
    
    /**
     * Gets all unsubscribed subscribers who have unsubscribed since the given date
     * @param string $added_since The date to start getting subscribers from
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => array(
     *         array(
     *             'EmailAddress' => The email address of the subscriber
     *             'Name' => The name of the subscriber
     *             'Date' => The date that the subscriber unsubscribed from the list
     *             'State' => The current state of the subscriber, will be 'Unsubscribed'
     *             'CustomFields' => array (
     *                 array(
     *                     'Key' => The personalisation tag of the custom field
     *                     'Value' => The value of the custom field for this subscriber
     *                 )
     *             )
     *         )
     *     )
     * )
     */
	function get_unsubscribed_subscribers($unsubscribed_since, $call_options = array()) {
		$call_options['route'] = $this->_lists_base_route.'unsubscribed.'.
		    $this->_serialiser->get_format().'?date='.urlencode($unsubscribed_since);
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);		
	}
    
    /**
     * Gets the basic details of the current list
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => array(
     *         'ListID' => The id of the list
     *         'Title' => The title of the list
     *         'UnsubscribePage' => The page which subscribers are redirected to upon unsubscribing
     *         'ConfirmationSuccessPage' => The page which subscribers are 
     *             redirected to upon confirming their subscription
     *         'ConfirmedOptIn' => Whether the list is Double-Opt In
     *     )
     * )
     */
	function get($call_options = array()) {
		$call_options['route'] = trim($this->_lists_base_route, '/').'.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);		
	}
    
    /**
     * Gets statistics for list subscriptions, deletions, bounces and unsubscriptions
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => array(
     *         'TotalActiveSubscribers' 
     *         'NewActiveSubscribersToday' 
     *         'NewActiveSubscribersYesterday'
     *         'NewActiveSubscribersThisWeek' 
     *         'NewActiveSubscribersThisMonth'
     *         'NewActiveSubscribersThisYeay' 
     *         'TotalUnsubscribes' 
     *         'UnsubscribesToday' 
     *         'UnsubscribesYesterday'
     *         'UnsubscribesThisWeek'
     *         'UnsubscribesThisMonth'
     *         'UnsubscribesThisYear'
     *         'TotalDeleted'
     *         'DeletedToday'
     *         'DeletedYesterday'
     *         'DeletedThisWeek'
     *         'DeletedThisMonth'
     *         'DeletedThisYear'
     *         'TotalBounces'
     *         'BouncesToday'
     *         'BouncesYesterday'
     *         'BouncesThisWeek'
     *         'BouncesThisMonth'
     *         'BouncesThisYear'
     *     )
     * )
     */
	function get_stats($call_options = array()) {
		$call_options['route'] = $this->_lists_base_route.'stats.'.$this->_serialiser->get_format();
		$call_options['method'] = CS_REST_GET;
		
		return $this->_call($call_options);		
	}
}