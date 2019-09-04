<?php
require_once dirname(__FILE__).'/class/base_classes.php';

/**
 * Class to access a journey resource from the create send API.
 * This class includes functions to retrieve journey information,such as summaries,
 * recipients, opens, clicks etc.
 * @author peterv
 *
 */

if (!class_exists('CS_REST_Journeys')) {
    class CS_REST_Journeys extends CS_REST_Wrapper_Base {

    	 /**
         * The base route of the lists resource.
         * @var string
         * @access private
         */
        var $_journeys_base_route;

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
	        $journey_id,
	        $auth_details,
	        $protocol = 'https',
	        $debug_level = CS_REST_LOG_NONE,
	        $host = 'api.createsend.com',
	        $log = NULL,
	        $serialiser = NULL,
	        $transport = NULL) {

	            parent::__construct($auth_details, $protocol, $debug_level, $host, $log, $serialiser, $transport);
	            $this->set_journey_id($journey_id);
        }


        /**
         * Change the email id used for calls after construction
         * @param $email_id
         * @access public
         */
        function set_journey_id($journey_id) {
            $this->_journeys_base_route = $this->_base_route.'journeys/'.$journey_id;
        }

         /**
         * Gets the details of the current journey
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be an object of the form
         * {
         *       'JourneyID' => The journey id 
         *       'Name' => The name of the journey
         *       'TriggerType' => The method in which the journey was triggered
         *       'Status' => The status of the journey
         *       'Emails' => array(
         *           {
         *               'EmailID' => The ID of the email attached to the journey
         *               'Name' => The name of the email attached to the journey
         *               'Bounced' => The number of recipients who bounced
         *               'Clicked' => The total number of recorded clicks
         *               'Opened' => The total number of recorded opens
         *               'Sent' => The total recipients of the journey email
         *               'UniqueOpened' => The number of recipients who opened
         *               'Unsubscribed' => The number of recipients who unsubscribed
         *           }
         *         )
         *
         */
 
        function get_journey_summary() {        
            return $this->get_request(trim($this->_journeys_base_route, '/').'.json');
        }
	}

}
