<?php
require_once dirname(__FILE__).'/class/base_classes.php';

/**
 * Class to access a journey resource from the create send API.
 * This class includes functions to retrieve journey information,such as summaries,
 * recipients, opens, clicks etc.
 * @author peterv
 *
 */

if (!class_exists('CS_REST_JourneyEmails')) {
    class CS_REST_JourneyEmails extends CS_REST_Wrapper_Base {

    	 /**
         * The base route of the lists resource.
         * @var string
         * @access private
         */
        var $_journey_emails_base_route;

        /**
         * Constructor.
         * @param $journey_id string The journey id to access
         * @param $auth_details array Authentication details to use for API calls.
         *        This array must take one of the following forms:
         *        If using OAuth to authenticate:
         *        array(
         *          'access_token' => 'your access token',
         *          'refresh_token' => 'your refresh token')
         *
         *        Or if using an API key:
         *        array('api_key' => 'your api key')
         * @param $protocol string The protocol to use for requests (http|https)
         * @param $debug_level int The level of debugging required CS_REST_LOG_NONE | CS_REST_LOG_ERROR | CS_REST_LOG_WARNING | CS_REST_LOG_VERBOSE
         * @param $host string The host to send API requests to. There is no need to change this
         * @param $log CS_REST_Log The logger to use. Used for dependency injection
         * @param $serialiser The serialiser to use. Used for dependency injection
         * @param $transport The transport to use. Used for dependency injection
         * @access public
         */

        function __construct (
	        $email_id,
	        $auth_details,
	        $protocol = 'https',
	        $debug_level = CS_REST_LOG_NONE,
	        $host = 'api.createsend.com',
	        $log = NULL,
	        $serialiser = NULL,
	        $transport = NULL) {

	            parent::__construct($auth_details, $protocol, $debug_level, $host, $log, $serialiser, $transport);
	            $this->set_email_id($email_id);
        }


        /**
         * Change the email id used for calls after construction
         * @param $email_id
         * @access public
         */
        function set_email_id($email_id) {
            $this->_journey_emails_base_route = $this->_base_route.'journeys/email/'.$email_id;
        }



         /**
         * Gets all email addresses from the journey email id specified
         * @param string $since The date to start getting bounces from
         * @param int $page_number The page number to get
         * @param int $page_size The number of records per page
         * @param string $order_field The field to order the record set by ('EMAIL', 'LIST')
         * @param string $order_direction The direction to order the record set ('ASC', 'DESC')
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
         *             'EmailAddress' => The suppressed email address
         *             'ListID' => The ID of the list this subscriber comes from
         *         }
         *     )
         * }
         */

        function get_journey_recipients($since = '', $page_number = NULL, $page_size = NULL, $order_field = NULL, 
            $order_direction = NULL) {        
        	return $this->get_request_paged($this->_journey_emails_base_route.'/recipients.json?date='.urlencode($since), $page_number, 
                $page_size, $order_field, $order_direction);
        }


        /**
         * Gets all recipients from the journey email id specified
         * @param string $since The date to start getting bounces from
         * @param int $page_number The page number to get
         * @param int $page_size The number of records per page
         * @param string $order_field The field to order the record set by ('EMAIL', 'LIST')
         * @param string $order_direction The direction to order the record set ('ASC', 'DESC')
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
         *             'EmailAddress' =>
		 *			   'Date' =>
		 *			   'IPAddress' =>
		 *			   'Latitude' =>
		 *			   'Longitude' =>
		 *			   'CountryName' =>
         *         }
         *     )
         * }
         */

        function get_journey_opens($since = '', $page_number = NULL, $page_size = NULL, $order_field = NULL, 
            $order_direction = NULL) {
        	return $this->get_request_paged($this->_journey_emails_base_route.'/opens.json?date='.urlencode($since), $page_number, 
                $page_size, $order_field, $order_direction);
        }

        /**
         * Gets all recipients who opened the journey email
         * @param string $since The date to start getting bounces from
         * @param int $page_number The page number to get
         * @param int $page_size The number of records per page
         * @param string $order_field The field to order the record set by ('EMAIL', 'LIST')
         * @param string $order_direction The direction to order the record set ('ASC', 'DESC')
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
					'EmailAddress' =>
					'Date' =>
					'URL' =>
					'IPAddress' =>
					'Latitude' =>
					'Longitude' =>
					'CountryName' =>
         *         }
         *     )
         * }
         */

        function get_journey_clicks($since = '', $page_number = NULL, $page_size = NULL, $order_field = NULL, 
            $order_direction = NULL) {        
        	return $this->get_request_paged($this->_journey_emails_base_route.'/clicks.json?date='.urlencode($since), $page_number, 
                $page_size, $order_field, $order_direction);
        }

        /**
         * Gets all recipients who unsubscribed from the journey email
         * @param string $since The date to start getting bounces from
         * @param int $page_number The page number to get
         * @param int $page_size The number of records per page
         * @param string $order_field The field to order the record set by ('EMAIL', 'LIST')
         * @param string $order_direction The direction to order the record set ('ASC', 'DESC')
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
         *             'EmailAddress' =>
         *             'Date' =>
         *			   'IPAddress' =>
         *         }
         *     )
         * }
         */
        function get_journey_unsubscribes($since = '', $page_number = NULL, $page_size = NULL, $order_field = NULL, 
            $order_direction = NULL) {        
        	return $this->get_request_paged($this->_journey_emails_base_route.'/unsubscribes.json?date='.urlencode($since), $page_number, 
                $page_size, $order_field, $order_direction);
        }

        /**
         * Gets all recipients who bounced from the journey email send
         * @param string $since The date to start getting bounces from
         * @param int $page_number The page number to get
         * @param int $page_size The number of records per page
         * @param string $order_field The field to order the record set by ('EMAIL', 'LIST')
         * @param string $order_direction The direction to order the record set ('ASC', 'DESC')
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
					'EmailAddress' =>
					'BounceType' =>
					'Date' =>
					'Reason' =>
         *         }
         *     )
         * }
         */

        function get_journey_bounces($since = '', $page_number = NULL, $page_size = NULL, $order_field = NULL, 
            $order_direction = NULL) {        
        	return $this->get_request_paged($this->_journey_emails_base_route.'/bounces.json?date='.urlencode($since), $page_number, 
                $page_size, $order_field, $order_direction);
        }


	}

}
