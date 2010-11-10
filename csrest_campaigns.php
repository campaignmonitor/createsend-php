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
        $this->set_campaign_id($campaign_id);
    }

    /**
     * Change the campaign id used for calls after construction
     * @param $campaign_id
     * @access public
     */
    function set_campaign_id($campaign_id) {
        $this->_campaigns_base_route = $this->_base_route.'campaigns/'.$campaign_id.'/';
    }

    /**
     * Creates a new campaign based on the provided campaign info.
     * At least on of the ListIDs and Segments parameters must be provided
     * @param string $client_id The client to create the campaign for
     * @param array $campaign_info The campaign information to use during creation.
     *     This array should be of the form
     *     array(
     *         'Subject' => string required The campaign subject
     *         'Name' => string required The campaign name
     *         'FromName' => string required The From name for the campaign
     *         'FromEmail' => string required The From address for the campaign
     *         'ReplyTo' => string required The Reply-To address for the campaign
     *         'HtmlUrl' => string required A url to download the campaign HTML from
     *         'TextUrl' => string optional A url to download the campaign text version from
     *         'ListIDs' => array<string> optional An array of list ids to send the campaign to
     *         'SegmentIDs' => array<string> optional An array of segment ids to send the campaign to.
     *     )
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (201)
     *     'response' => string The ID of the newly created campaign
     * )
     */
    function create($client_id, $campaign_info, $call_options = array()) {
        $call_options['route'] = $this->_base_route.'campaigns/'.$client_id.'.json';
        $call_options['method'] = CS_REST_POST;
        $call_options['data'] = $this->_serialiser->serialise($campaign_info);

        return $this->_call($call_options);
    }
    
    /**
     * Sends a preview of an existing campaign to the specified recipients. 
     * @param array<string> $recipients The recipients to send the preview to. 
     * @param string $personalize How to personalize the campaign content. Valid options are:
     *     'Random': Choose a random campaign recipient and use their personalisation data
     *     'Fallback': Use the fallback terms specified in the campaign content
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP response code (200)
     *     'response' => string The HTTP response (It will be empty)
     * )
     */
    function send_preview($recipients, $personalize = 'Random', $call_options = array()) {        
        $call_options['route'] = $this->_campaigns_base_route.'sendpreview.json';
        $call_options['method'] = CS_REST_POST;
        $call_options['data'] = $this->_serialiser->serialise($test_data);
        
        return $this->_call($call_options);
    }

    /**
     * Sends an existing campaign based on the scheduling information provided
     * @param array $schedule The campaign scheduling information.
     *     This array should be of the form
     *     array (
     *        'ConfirmationEmail' => string required The email address to send a confirmation email to,
     *        'SendDate' => string required The date to send the campaign or 'immediately'.
     *                      The date should be in the format 'y-M-d'
     *     )
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => string The HTTP Response (It will be empty)
     * )
     */
    function send($schedule, $call_options = array()) {
        $call_options['route'] = $this->_campaigns_base_route.'send.json';
        $call_options['method'] = CS_REST_POST;
        $call_options['data'] = $this->_serialiser->serialise($schedule);

        return $this->_call($call_options);
    }

    /**
     * Deletes an existing campaign from the system
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => string The HTTP Response (It will be empty)
     * )
     */
    function delete($call_options = array()) {
        $call_options['route'] = trim($this->_campaigns_base_route, '/').'.json';
        $call_options['method'] = CS_REST_DELETE;

        return $this->_call($call_options);
    }

    /**
     * Gets all email addresses on the current clients suppression list
     * @param int $page_number The page number to get
     * @param int $page_size The number of records per page
     * @param string $order_field The field to order the record set by ('EMAIL', 'LIST')
     * @param string $order_direction The direction to order the record set ('ASC', 'DESC')
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => array(
     *         'ResultsOrderedBy' => The field the results are ordered by
     *         'OrderDirection' => The order direction
     *         'PageNumber' => The page number for the result set
     *         'PageSize' => The page size used
     *         'RecordsOnThisPage' => The number of records returned
     *         'TotalNumberOfRecords' => The total number of records available
     *         'NumberOfPages' => The total number of pages for this collection
     *         'Results' => array(
     *             array(
     *                 'EmailAddress' => The suppressed email address
     *                 'ListID' => The ID of the list this subscriber comes from
     *             )
     *         )
     *     )
     * )
     */
    function get_recipients($page_number = NULL, $page_size = NULL, $order_field = NULL, 
        $order_direction = NULL, $call_options = array()) {
            
        $route = $this->_campaigns_base_route.'recipients.json';
        
        $call_options['route'] = $this->_add_paging_to_route($route, $page_number, $page_size, 
            $order_field, $order_direction, '?');
        $call_options['method'] = CS_REST_GET;
        
        return $this->_call($call_options);
    }

    /**
     * Gets all bounces recorded for a campaign
     * @param int $page_number The page number to get
     * @param int $page_size The number of records per page
     * @param string $order_field The field to order the record set by ('EMAIL', 'LIST', 'DATE')
     * @param string $order_direction The direction to order the record set ('ASC', 'DESC')
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => array(
     *         'ResultsOrderedBy' => The field the results are ordered by
     *         'OrderDirection' => The order direction
     *         'PageNumber' => The page number for the result set
     *         'PageSize' => The page size used
     *         'RecordsOnThisPage' => The number of records returned
     *         'TotalNumberOfRecords' => The total number of records available
     *         'NumberOfPages' => The total number of pages for this collection
     *         'Results' => array(
     *             array(
     *                 'EmailAddress' => The email that bounced
     *                 'ListID' => The ID of the list the subscriber was on
     *                 'BounceType' => The type of bounce
     *                 'Date' => The date the bounce message was received
     *                 'Reason' => The reason for the bounce
     *             )
     *         )
     *     )
     * )
     */
    function get_bounces($page_number = NULL, $page_size = NULL, $order_field = NULL, 
        $order_direction = NULL, $call_options = array()) {
            
        $route = $this->_campaigns_base_route.'bounces.json';
        
        $call_options['route'] = $this->_add_paging_to_route($route, $page_number, $page_size, 
            $order_field, $order_direction, '?');
        $call_options['method'] = CS_REST_GET;

        return $this->_call($call_options);
    }

    /**
     * Gets the lists a campaign was sent to
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => array(
     *         'Lists' =>  array(
     *             'ListID' => The list id
     *             'Name' => The list name
     *         ), 
     *         'Segments' => array(
     *             'ListID' => The list id of the segment
     *             'SegmentID' => The id of the segment
     *             'Title' => The title of the segment
     *         )
     *     )
     * )
     */
    function get_lists_and_segments($call_options = array()) {
        $call_options['route'] = $this->_campaigns_base_route.'listsandsegments.json';
        $call_options['method'] = CS_REST_GET;

        return $this->_call($call_options);
    }

    /**
     * Gets a summary of all campaign reporting statistics
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => array(
     *         'Recipients' => The total recipients of the campaign
     *         'TotalOpened' => The total number of opens recorded
     *         'Clicks' => The total number of recorded clicks
     *         'Unsubscribed' => The number of recipients who unsubscribed
     *         'Bounced' => The number of recipients who bounced
     *         'UniqueOpened' => The number of recipients who opened
     *         'WebVersionURL' => The url of the webversion of the campaign
     *     )
     * )
     */
    function get_summary($call_options = array()) {
        $call_options['route'] = $this->_campaigns_base_route.'summary.json';
        $call_options['method'] = CS_REST_GET;

        return $this->_call($call_options);
    }

    /**
     * Gets all opens recorded for a campaign since the provided date
     * @param string $since The date to start getting opens from
     * @param int $page_number The page number to get
     * @param int $page_size The number of records per page
     * @param string $order_field The field to order the record set by ('EMAIL', 'LIST', 'DATE')
     * @param string $order_direction The direction to order the record set ('ASC', 'DESC')
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => array(
     *         'ResultsOrderedBy' => The field the results are ordered by
     *         'OrderDirection' => The order direction
     *         'PageNumber' => The page number for the result set
     *         'PageSize' => The page size used
     *         'RecordsOnThisPage' => The number of records returned
     *         'TotalNumberOfRecords' => The total number of records available
     *         'NumberOfPages' => The total number of pages for this collection
     *         array(
     *             array(
     *                 'EmailAddress' => The email address of the subscriber who opened
     *                 'ListID' => The list id of the list containing the subscriber
     *                 'Date' => The date of the open
     *                 'IPAddress' => The ip address where the open originated
     *             )
     *         )
     *     )
     * )
     */
    function get_opens($since, $page_number = NULL, $page_size = NULL, $order_field = NULL, 
        $order_direction = NULL, $call_options = array()) {
            
        $route = $this->_campaigns_base_route.'opens.json?date='.urlencode($since);
        
        $call_options['route'] = $this->_add_paging_to_route($route, $page_number, $page_size, 
            $order_field, $order_direction);
        $call_options['method'] = CS_REST_GET;

        return $this->_call($call_options);
    }

    /**
     * Gets all clicks recorded for a campaign since the provided date
     * @param string $since The date to start getting clicks from
     * @param int $page_number The page number to get
     * @param int $page_size The number of records per page
     * @param string $order_field The field to order the record set by ('EMAIL', 'LIST', 'DATE')
     * @param string $order_direction The direction to order the record set ('ASC', 'DESC')
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => array(
     *         'ResultsOrderedBy' => The field the results are ordered by
     *         'OrderDirection' => The order direction
     *         'PageNumber' => The page number for the result set
     *         'PageSize' => The page size used
     *         'RecordsOnThisPage' => The number of records returned
     *         'TotalNumberOfRecords' => The total number of records available
     *         'NumberOfPages' => The total number of pages for this collection
     *         array(
     *             array(
     *                 'EmailAddress' => The email address of the subscriber who clicked
     *                 'ListID' => The list id of the list containing the subscriber
     *                 'Date' => The date of the click
     *                 'IPAddress' => The ip address where the click originated
     *                 'URL' => The url that the subscriber clicked on
     *             )
     *         )
     *     )
     * )
     */
    function get_clicks($since, $page_number = NULL, $page_size = NULL, $order_field = NULL, 
        $order_direction = NULL, $call_options = array()) {
            
        $route = $this->_campaigns_base_route.'clicks.json?date='.urlencode($since);
        
        $call_options['route'] = $this->_add_paging_to_route($route, $page_number, $page_size, 
            $order_field, $order_direction);
        $call_options['method'] = CS_REST_GET;

        return $this->_call($call_options);
    }

    /**
     * Gets all unsubscribes recorded for a campaign since the provided date
     * @param string $since The date to start getting unsubscribes from
     * @param int $page_number The page number to get
     * @param int $page_size The number of records per page
     * @param string $order_field The field to order the record set by ('EMAIL', 'LIST', 'DATE')
     * @param string $order_direction The direction to order the record set ('ASC', 'DESC')
     * @param $call_options
     * @access public
     * @return A successful call will return an array of the form array(
     *     'code' => int The HTTP Response Code (200)
     *     'response' => array(
     *         'ResultsOrderedBy' => The field the results are ordered by
     *         'OrderDirection' => The order direction
     *         'PageNumber' => The page number for the result set
     *         'PageSize' => The page size used
     *         'RecordsOnThisPage' => The number of records returned
     *         'TotalNumberOfRecords' => The total number of records available
     *         'NumberOfPages' => The total number of pages for this collection
     *         array(
     *             array(
     *                 'EmailAddress' => The email address of the subscriber who unsubscribed
     *                 'ListID' => The list id of the list containing the subscriber
     *                 'Date' => The date of the unsubscribe
     *                 'IPAddress' => The ip address where the unsubscribe originated
     *             )
     *         )
     *     )
     * )
     */
    function get_unsubscribes($since, $page_number = NULL, $page_size = NULL, $order_field = NULL, 
        $order_direction = NULL, $call_options = array()) {
        
        $route = $this->_campaigns_base_route.'unsubscribes.json?date='.urlencode($since);
        
        $call_options['route'] = $this->_add_paging_to_route($route, $page_number, $page_size, 
            $order_field, $order_direction);
        $call_options['method'] = CS_REST_GET;

        return $this->_call($call_options);
    }
}