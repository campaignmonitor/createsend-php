<?php
require_once 'class/base_classes.php';

/**
 * Class to access a segments resources from the create send API.
 * This class includes functions to create and edits segments
 * along with accessing the subscribers of a specific segment
 * @author tobyb
 *
 */
class CS_REST_Segments extends CS_REST_Wrapper_Base {

    /**
     * The base route of the lists resource.
     * @var string
     * @access private
     */
    var $_segments_base_route;

    /**
     * Constructor.
     * @param $segment_id string The segment id to access (Ignored for create requests)
     * @param $api_key string Your api key (Ignored for get_apikey requests)
     * @param $protocol string The protocol to use for requests (http|https)
     * @param $debug_level int The level of debugging required CS_REST_LOG_NONE | CS_REST_LOG_ERROR | CS_REST_LOG_WARNING | CS_REST_LOG_VERBOSE
     * @param $host string The host to send API requests to. There is no need to change this
     * @param $log CS_REST_Log The logger to use. Used for dependency injection
     * @param $serialiser The serialiser to use. Used for dependency injection
     * @param $transport The transport to use. Used for dependency injection
     * @access public
     */
    function CS_REST_Segments (
    $segment_id,
    $api_key,
    $protocol = 'https',
    $debug_level = CS_REST_LOG_NONE,
    $host = 'api.createsend.com',
    $log = NULL,
    $serialiser = NULL,
    $transport = NULL) {
            
        $this->CS_REST_Wrapper_Base($api_key, $protocol, $debug_level, $host, $log, $serialiser, $transport);
        $this->set_segment_id($segment_id);
    }

    /**
     * Change the segment id used for calls after construction
     * @param $segment_id
     * @access public
     */
    function set_segment_id($segment_id) {
        $this->_segments_base_route = $this->_base_route.'segments/'.$segment_id.'/';
    }

    /**
     * Deletes an existing segment from the system
     * @param $call_options
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be empty
     */
    function delete($call_options = array()) {
        $call_options['route'] = trim($this->_segments_base_route, '/').'.json';
        $call_options['method'] = CS_REST_DELETE;
        
        return $this->_call($call_options);
    }

    /**
     * Deletes all rules for the current segment
     * @param $call_options
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be empty
     */
    function clear_rules($call_options = array()) {
        $call_options['route'] = $this->_segments_base_route.'rules.json';
        $call_options['method'] = CS_REST_DELETE;
        
        return $this->_call($call_options);
    }
    
    /**
     * Gets a paged collection of subscribers which fall into the given segment
     * @param string $subscribed_since The date to start getting subscribers from 
     * @param int $page_number The page number to get
     * @param int $page_size The number of records per page
     * @param string $order_field The field to order the record set by ('EMAIL', 'NAME', 'DATE')
     * @param string $order_direction The direction to order the record set ('ASC', 'DESC')
     * @param $call_options
     * @access public
     * @return CS_REST_Wrapper_Result A successful response will be an object of the form
     * {
     *     'ResultsOrderedBy' => The field the results are ordered by
     *     'OrderDirection' => The order direction
     *     'PageNumber' => The page number for the result set
     *     'PageSize' => The page size used
     *     'RecordsOnThisPage' => The number of records returned
     *     'TotalNumberOfRecords' => The total number of records available
     *     'NumberOfPages' => The total number of pages for this collection
     *     'Results' => array(
     *         {
     *             'EmailAddress' => The email address of the subscriber
     *             'Name' => The name of the subscriber
     *             'Date' => The date that the subscriber was added to the list
     *             'State' => The current state of the subscriber, will be 'Active'
     *             'CustomFields' => array (
     *                 {
     *                     'Key' => The personalisation tag of the custom field
     *                     'Value' => The value of the custom field for this subscriber
     *                 }
     *             )
     *         }
     *     )
     * }
     */
    function get_subscribers($subscribed_since, $page_number = NULL, 
        $page_size = NULL, $order_field = NULL, $order_direction = NULL, $call_options = array()) {
            
        $route = $this->_segments_base_route.'active.json?date='.urlencode($subscribed_since);
        
        $call_options['route'] = $this->_add_paging_to_route($route, $page_number, 
            $page_size, $order_field, $order_direction);
        $call_options['method'] = CS_REST_GET;
        
        return $this->_call($call_options);
    }
}