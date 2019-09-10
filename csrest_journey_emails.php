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
         * @param string $order_field Not used 
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
         *             'EmailAddress' => The email address of the subscriber
         *             'SentDate' => The date the subscriber was sent the mailing
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
         * @param string $order_field Not used 
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
         *             'EmailAddress' => The email address of the subscriber who opened
         *             'Date' => The date of the open
         *             'IPAddress' => The ip address where the open originated
         *             'Latitude' => The geocoded latitude from the IP address
         *             'Longitude' => The geocoded longitude from the IP address
         *             'City' => The geocoded city from the IP address
         *             'Region' => The geocoded region from the IP address
         *             'CountryCode' => The geocoded two letter country code from the IP address
         *             'CountryName' => The geocoded full country name from the IP address
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
         * @param string $order_field Not used 
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
         *             'EmailAddress' => The email address of the subscriber who clicked
         *             'Date' => The date of the click
         *             'URL' => The URL of the link that was clicked
         *             'IPAddress' => The ip address where the click originated
         *             'Latitude' => The geocoded latitude from the IP address
         *             'Longitude' => The geocoded longitude from the IP address
         *             'City' => The geocoded city from the IP address
         *             'Region' => The geocoded region from the IP address
         *             'CountryCode' => The geocoded two letter country code from the IP address
         *             'CountryName' => The geocoded full country name from the IP address
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
         * @param string $order_field Not used 
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
         *             'EmailAddress' => The email address of the subscriber who unsubscribed
         *             'Date' => The date of the unsubscribe
         *			   'IPAddress' => The ip address where the unsubscribe originated
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
         * @param string $order_field Not used 
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
		 *			'EmailAddress' => The email address of the subscriber who unsubscribed
		 *			'BounceType' => The bounce type 
		 *			'Date' => The date of the bounce
		 *			'Reason' => The reason for the bounce 
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
