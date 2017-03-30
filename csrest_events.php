<?php
require_once dirname(__FILE__).'/class/base_classes.php';

/**
 * Class to send event data to the create send API.
 * @author philoye
 *
 */
if (!class_exists('CS_REST_Events')) {
    class CS_REST_Events extends CS_REST_Wrapper_Base {

        /**
         * The base route of the clients resource.
         * @var string
         * @access private
         */
        var $_events_base_route;


        /**
         * Constructor.
         * @param $client_id string The client id to send email on behalf of
         *        Optional if using a client api key
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
        $auth_details,
        $client_id,
        $protocol = 'https',
        $debug_level = CS_REST_LOG_NONE,
        $host = 'api.createsend.com',
        $log = NULL,
        $serialiser = NULL,
        $transport = NULL) {
            parent::__construct($auth_details, $protocol, $debug_level, $host, $log, $serialiser, $transport);
            $this->set_client_id($client_id);
        }



        /**
         * Change the client id used for calls after construction
         * @param $client_id
         * @access public
         */
        function set_client_id($client_id) {
            $this->_events_base_route = $this->_base_route.'events/'.$client_id.'/';
        }

        /**
         * Tracks an event
         * @param string $email required email in the form "user@example.com"
         * 
         * @param string $event_type. Name to group events by for reporting
         *    For example "Page View", "Order confirmation"
         * @param array $data optional. Event payload.
         *      This should be an array, each property is optionals
         *          array(
         *            RandomFieldName  => whether to track opens, defaults to true
         *            RandomFieldURL => whether to track clicks, defaults to true
         *            InlineCSS   => whether inline CSS, defaults to true
         *            RandomArray => ID of a list to add all recipeints to
         *          )
         * @access public
         * @return CS_REST_Wrapper_Result A successful response will be the include the details of the action, including a Event ID.
         *      array(
         *          array(
         *              "EventID" => string
         *          )
         *      )
         */
        function track($email, $event_type, $data = NULL) {
            if (!isset($email)) {
                trigger_error('$email needs to be set in and a valid array');
			    exit;
            }
            if (!isset($event_type)) {
                trigger_error('$event_type needs to be set');
			    exit;
            }
            if (!is_array($data)){
                trigger_error('$data needs to be a valid array');
			    exit;
            } 
            $payload = array_merge(array('ContactID' => array('Email' => $email)), array('Name' => $event_type), array('Data' => $data));
            return $this->post_request($this->_events_base_route. 'track', $payload);
        }
    }
}